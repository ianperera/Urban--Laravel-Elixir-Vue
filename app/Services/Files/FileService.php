<?php

namespace App\Services\Files;

use Illuminate\Http\UploadedFile;
use Storage;
use Store;
use Carbon\Carbon;
use Exception;
use Uuid;
use Log;
use Auth;
use Entrust;
use Bnb\PdfToImage;
use App\Models\File;
use Intervention\Image\Facades\Image;


class FileService
{
    public $messages;
    public $errors;
    public $files;

    /**
     * @param array $uploadedFiles
     * @param array $storable
     * @param array $options
     * @return $this
     */
    public function store(array $uploadedFiles, array $storable, array $options = []): FileService
    {
        if (is_array($uploadedFiles)) {
            foreach ($uploadedFiles as $uploadedFile) {
                $this->save($uploadedFile, $storable, $options);
            }
        } else {
            $this->save($uploadedFiles, $storable, $options);
        }

        return $this;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param array $storable
     * @param array $options
     * @return FileService
     */
    public function save(UploadedFile $uploadedFile, array $storable, array $options = []): FileService
    {
        if (!$uploadedFile->isValid())
            return response()->json($uploadedFile->getErrorMessage());

        $storable['name'] = $uploadedFile->getClientOriginalName();
        $storable['ext'] = $uploadedFile->getClientOriginalExtension();
        $this->add($uploadedFile->getRealPath(), $storable, $options);
        return $this;
    }

    /**
     * Add file to storage, based on filepath and storable data
     * @param $filePath
     * @param $storable
     * @param array $options
     * @return $this
     */
    public function add(string $filePath, array $storable, array $options = [])
    {
        $userId = $storable['user_id'];
        $storableKey = $storable['key'];
        $storableType = $storable['type'];
        $storableId = $storable['id'];
        $categoryId = $storable['category_id'] ?? null;
        $fileName = $storable['name'] ?? substr($filePath, strrpos($filePath, '/') + 1);

        try {
            $fs = new \Symfony\Component\HttpFoundation\File\File($filePath, true);
            $origFileName = $storable['name'] ?? $fs->getFilename();
            $fileExt = $storable['ext'] ?? $fs->getExtension();
            $fileSize = $fs->getSize();
            $mime = $fs->getMimeType();

            // optimize pdf within ghost script (realy required for hellosign pdf - x10 size after esigns + SN timestamp campatibility)
            if ($fileExt === 'pdf') {
                $fs = $this->optimizePDF($filePath);
                $origFileName = $storable['name'] ?? $fs->getFilename();
                $fileExt = $storable['ext'] ?? $fs->getExtension();
                $fileSize = $fs->getSize();
                $mime = $fs->getMimeType();
            }

            $dirPath = "/{$storableType}/{$storableKey}/";
            $fileDir = storage_path('app/public') . $dirPath;
            $fileName = $origFileName;
            $fullPath = $fileDir . $fileName;

            Storage::makeDirectory('public' . $dirPath);
            $fs->move($fileDir, $fileName);

            $type = $this->getFileType($mime);

            // if image reacieved from QRbuilt it fill resiesed to 600*auto
            $optimizeQrFiles = array_get($options, 'optimize_qr_files', false);
            if ($optimizeQrFiles === true && $type === 'image') {
                $this->resizeQRimage($fullPath);
            }

            if ($fileExt === 'pdf' && $categoryId == 'signed_building_configuration') {
                $fileName = str_replace('.pdf', '', $origFileName) . '.jpg';
                $pdf = new PdfToImage\Pdf($fullPath);
                $pdf->setResolution(300);
                $fullPath = $fileDir . $fileName;
                $pdf->saveImage($fullPath);

                $fs = new \Symfony\Component\HttpFoundation\File\File($fullPath, true);
                $fileSize = $fs->getSize();

                $mime = 'image/jpeg';
                $type = $this->getFileType($mime);
                $fileExt = 'jpg';
            }

            $overwriteFile = array_get($options, 'overwrite', false);
            if ($overwriteFile === true) {
                $found = File::where('path', $dirPath)
                    ->where('name', $fileName)
                    ->where('storable_id', $storableId)
                    ->where('storable_type', $storableType)
                    ->first();
                if ($found) {
                    $file = $found;
                    $file->updated_at = date('Y-m-d H:i:s');
                }
            }

            $file = $file ?? new File;
            $file->user_id = $userId;
            $file->storable_id = $storableId;
            $file->storable_type = $storableType;
            $file->category_id = $categoryId;
            $file->type = $type;
            $file->mime = $mime;
            $file->path = $dirPath;
            $file->name = $fileName;
            $file->ext = $fileExt;
            // $file->description = null;
            // $file->source_id = null;
            // $file->reason = null;
            $file->size = $fileSize;

            if ($type === 'image') {
                $dimensions = getimagesize($fullPath);
                $file->width = $dimensions[0];
                $file->height = $dimensions[1];
            }
            $file->save();

            $this->messages[] = "File \"{$origFileName}\" successfully added.";
            $this->files[] = $file;
            return $this;
        } catch (Exception $e) {
            Log::error($e);
            $this->errors[] = "File \"{$fileName}\" has not been added.";
            return $this;
        }

        $this->errors[] = "File \"{$fileName}\" has not been added.";
        return $this;
    }

    public function delete($files)
    {
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            if ($file->storable_type === 'order') {
                if (in_array($file->storable->status_id, ['draft', 'review_needed']) || Entrust::hasRole('administrator')) {
                    $file->delete(); // safe delete (without drop from hard drive for now)
                    $this->messages[] = "File \"{$file->name}\" successfully removed.";
                } else {
                    $this->errors[] = "You have no privilege to remove \"{$file->name}\" file.";
                }
            } else {
                if (Entrust::hasRole('administrator')) {
                    $file->delete();
                } else {
                    $this->errors[] = "You have no privilege to remove \"{$file->name}\" file.";
                }
            }
        }

        return $this;
    }

    public function success()
    {
        return count($this->errors) === 0;
    }

    public function error()
    {
        return count($this->errors) !== 0;
    }

    /**
     * @param string $mime
     * @return array
     */
    private function getFileType(string $mime) {
        $type = explode('/', $mime);
        $type = ($type[0] === 'application') ? $type[1] : $type[0];
        return $type;
    }

    /**
     * Change file dimensions and save
     * @param resource $file
     * @return void
     */
    protected function resizeQRimage($file)
    {
        //  Image -> Intervention\Image\Facades\Image
        $img = Image::make($file);
        // resize the image to a width of 600 and constrain aspect ratio (auto height)

        if($img->width() > 600) {
            $img->resize(600, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        // save file as jpg with medium quality (60)
        $img->save($file, 60);
    }

    protected function optimizePDF($filePath)
    {
        $tmpfname = tempnam("/tmp", "FOO");
        if (!exec("gs -dAutoRotatePages=/None -sDEVICE=pdfwrite -sOutputFile='{$tmpfname}' -dNOPAUSE -dBATCH '{$filePath}'"))
            throw new Exception("PDF optimization failed.");

        return new \Symfony\Component\HttpFoundation\File\File($tmpfname, true);
    }
}