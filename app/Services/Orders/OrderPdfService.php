<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\OrderReference;
use App\Models\Building;
use App\Models\BuildingOptionColor;
use App\Models\Color;
use App\Models\Dealer;
use App\Models\File;

use App\Presenters\Files\ImagePdfPresenter;
use App\Presenters\Orders\OrderPdfPresenter;
use App\Presenters\Orders\OrderPdfWorkBuildingFormPresenter;
use App\Presenters\Orders\OrderPdfCustomerQuotePresenter;

use App\Services\Orders\OrderCalculator as OrderCalculator;

use Barryvdh\Snappy\PdfWrapper;
use App\Exceptions\BusinessException;
use PDF;
use stdClass;
use Storage;
use Helper;

class OrderPdfService
{
    /**
     * @var \App\Services\Orders\OrderCalculator
     */
    protected $orderCalculator;

    public function __construct(OrderCalculator $orderCalculator)
    {
        $this->orderCalculator = $orderCalculator;
    }

    /**
     * @return \App\Services\Orders\OrderCalculator
     */
    public function calculator(): OrderCalculator
    {
        return $this->orderCalculator;
    }

    /**
     * @param Order $order
     * @param Dealer $dealer
     * @param Building $building
     * @return OrderPdfService
     */
    public function calculateOrder(Order $order, Dealer $dealer, Building $building): OrderPdfService {
        $this->orderCalculator->setOrder($order);
        $this->orderCalculator->setDealer($dealer);
        $this->orderCalculator->setBuilding($building);
        $this->orderCalculator->calculateOrder();
        return $this;
    }

    /**
     * Generate document by category (file)
     * @param Order $order
     * @param string $categoryId
     * @return mixed
     * @throws BusinessException
     */
    public function generateDocument(Order $order, string $categoryId) {
        if ($categoryId === 'unsigned_order_documents') $pdf = $this->getUnsignedOrderDocuments($order);
        if ($categoryId === 'complete_order_documents') $pdf = $this->getCompleteOrderDocuments($order);
        if ($categoryId === 'building_configuration') $pdf = $this->getBuildingConfiguration($order);
        if ($categoryId === 'neighbor_release') $pdf = $this->getNeighborRelease($order);
        if ($categoryId === 'deposit_receipt') $pdf = $this->getDepositReceipt($order);
        if ($categoryId === 'quote_forms') $pdf = $this->getQuoteForms($order);
        if (!$pdf) throw new BusinessException('Category is not found.');

        return $pdf;
    }

    /**
     * Save Order PDF document based on category ID and Order
     * Return array of storage params for save file to model & DB
     * @param Order $order
     * @param PdfWrapper $pdfDocument
     * @param string $categoryId
     * @return array
     */
    public function saveDocument(Order $order, PdfWrapper $pdfDocument, string $categoryId): array {
        $orderPath = date('Y') . '/' . date('m') . '/';
        $publicDirectory = "/public/order/0/{$orderPath}";
        $storageDirectory = storage_path('app').$publicDirectory;

        $fileName = $this->getOrderDocumentNameByCategory($order, $categoryId);
        $storagePath = $storageDirectory . $fileName;

        // save document to temporarily directory (storagePath = temp dir)
        $pdfDocument->save($storagePath, true);
        $storageParams = [
            'user_id' => null, // $dealer->id,// Auth::user()->id,
            'type' => 'order',
            'id' => $order->id,
            'key' => $order->id,
            'category_id' => $categoryId,
            'path' => $storagePath,
            'name' => $fileName,
            'ext' => 'pdf',
        ];

        return $storageParams;
    }

    /**
     * @param Order $order
     * @param string $categoryId
     * @return string
     */
    public function getOrderDocumentNameByCategory(Order $order, string $categoryId): string {
        $prefix = '';
        $postfix = "_{$categoryId}";
        $fileName = "{$order->order_reference->last_name}, {$order->order_reference->first_name} {$order->building->serial_short_code}-{$order->building->serial_sizes}";
        $fileName.= $prefix;
        $fileName.= $postfix;
        $fileName.= '.pdf';
        return $fileName;
    }

    /**
     * Get pdf document (unsigned order documents)
     * Generated in /dealer-order-form
     * @param Order $order
     * @return PdfWrapper
     */
    public function getUnsignedOrderDocuments(Order $order): PdfWrapper {
        $pages = collect();
        $pages->push('order-main');
        $pages->push('delivery');

        // only RTO docs
        if ($order->payment_type === 'rto') {
            $pages->push('rto-renter-info');
            $pages->push('rto-agreement');
            $pages->push('rto-promo99');
        }
        if ($order->dr_must_cross_neighboring_property === true) {
            $pages->push('neighbor-release-form');
        }

        $order = $order->present(OrderPdfPresenter::class);
        $pdf = PDF::loadView('forms.order', [
            'dealer' => $order->dealer,
            'order' => $order,
            'orderReference' => $order->order_reference,
            'building' => $order->building,
            'params' => [],
            'sections' => $pages
        ]);
        $pdf->setPaper('Letter');
        return $pdf;
    }

    /**
     * Get pdf document (complete order documents)
     * Combine all pages + driver license
     * Generated in /dealer-order-form
     * @param Order $order
     * @return PdfWrapper
     * @throws BusinessException
     */
    public function getCompleteOrderDocuments(Order $order): PdfWrapper {
        $order = $order->present(OrderPdfPresenter::class);
        $params = [
            'dealer' => $order->dealer,
            'order' => $order,
            'orderReference' => $order->order_reference,
            'building' => $order->building,
            'params' => [],
        ];
        $pages = collect();
        $pages->push('order-main');

        // only RTO docs
        if ($order->payment_type === 'rto') {
            $pages->push('rto-renter-info');
            $pages->push('rto-agreement');

            if ($order->promo99 === true) {
                $pages->push('rto-promo99');
            }

            // rto (non-cash payment type is required driver license)
            $driverLicense = $order->files()->where('category_id', 'driver_license')->first();
            if (!$driverLicense) {
                throw new BusinessException(trans('exceptions.orders.document.generate.complete_order_documents.driver_license_is_not_found'));
            }

            $params['driverLicense'] = $driverLicense->present(ImagePdfPresenter::class)->resize(800, 1000);
            $pages->push('driver_license');
        }

        if ($order->dr_must_cross_neighboring_property === true) {
            $pages->push('neighbor-release-form');
        }

        if ($order->sale_type === 'custom-order') {
            // required signed buildign configuration is sale type is custom order
            $signedBuildingConfiguration = $order->files()->where('category_id', 'signed_building_configuration')->first();
            if (!$signedBuildingConfiguration) {
                throw new BusinessException(trans('exceptions.orders.document.generate.complete_order_documents.signed_building_configuration_is_not_found'));
            }

            $params['signedBuildingConfiguration'] = $signedBuildingConfiguration->present(ImagePdfPresenter::class)->resize(800, 1000);
            $pages->push('signed-building-configuration');
        }

        $params['sections'] = $pages;
        $pdf = PDF::loadView('forms.order', $params);
        $pdf->setPaper('Letter');
        return $pdf;
    }

    /**
     * Generate PDF building configuration
     * @return PdfWrapper
     */
    public function getBuildingConfiguration(): PdfWrapper {
        $pages = collect();
        $pages->push('order-grid');
        $pdf = PDF::loadView('forms.order', [
            'dealer' => new Dealer,
            'order' => new Order,
            'orderReference' => new OrderReference,
            'building' => new Building,
            'params' => [],
            'sections' => $pages
        ]);
        $pdf->setPaper('Letter');
        return $pdf;
    }

    /**
     * Generate PDF neighbot release
     * @return PdfWrapper
     */
    public function getNeighborRelease(): PdfWrapper {
        $pages = collect();
        $pages->push('order-grid');
        $pdf = PDF::loadView('forms.neighbor-release-form', [
            'sections' => $pages
        ]);
        $pdf->setPaper('Letter');
        return $pdf;
    }

    /**
     * Generate PDF Deposit Receipt
     * @param Order $order
     * @return PdfWrapper
     */
    public function getDepositReceipt(Order $order): PdfWrapper {
        $pages = collect();
        $pages->push('deposit-receipt');

        $order = $order->present(OrderPdfPresenter::class);
        $pdf = PDF::loadView('forms.order', [
            'dealer' => $order->dealer,
            'order' => $order,
            'orderReference' => $order->order_reference,
            'building' => $order->building,
            'params' => [],
            'sections' => $pages
        ]);
        $pdf->setPaper('Letter');
        return $pdf;
    }

    /**
     * Generate PDF Quote
     * @param Order $order
     * @return PdfWrapper
     */
    public function getQuoteForms(Order $order): PdfWrapper {
        if ($order->payment_type !== 'rto') {
            $order = $this->calculator()->setOrder($order)->calculateRtoAmount()->getOrder();
        }

        $order = $order->present(OrderPdfPresenter::class);
        $pdf = PDF::loadView('forms.quote', [
            'dealer' => $order->dealer,
            'order' => $order,
            'orderReference' => $order->order_reference,
            'building' => $order->building,
            'params' => []
        ]);
        $pdf->setPaper('Letter');
        return $pdf;
    }

    /**
     * Generate PDF inventory form by specified dealer
     * @param Building $building
     * @param Dealer $dealer
     * @return PdfWrapper
     */
    public function getInventoryForm(Building $building, Dealer $dealer): PdfWrapper
    {
        $order = $building->order ?? new Order();
        $order = $this->calculator()
            ->setOrder($order)
            ->setDealer($dealer)
            ->setBuilding($building)
            ->calculateOrder()
            ->calculateRtoAmount()
            ->getOrder();
        $order = $order->present(OrderPdfPresenter::class);

        $pdf = PDF::loadView('forms.inventory', [
            'dealer' => $dealer,
            'order' => $order,
            'orderReference' => new OrderReference,
            'building' => $building,
            'params' => [],
            'rtoTerms' => \App\Models\Order::$rtoTerms
        ]);
        $pdf->setPaper('Letter');

        return $pdf;
    }

    /**
     * Generate PDF inventory form, but replace colors to standard
     * Used in landing by route /inventory_building/{building_id}
     * @param Building $building
     * @param Dealer $dealer
     * @return PdfWrapper
     */
    public function getInitialInventoryForm(Building $building, Dealer $dealer): PdfWrapper
    {
        // search STANDARD COLOR for replacing
        // getting ONLY FIRST found color per each option category (roof/trim/siding/...)
        $colors = collect();
        foreach (['siding', 'trim', 'roof'] as $group) {
            $color = Color::with(['option'])->whereHas('allowable_options', function ($allowableOption) use ($group, $building) {
                $allowableOption->whereHas('category', function ($category) use($group) {
                    $category->where('group', $group);
                });
                $allowableOption->whereHas('allowable_models', function ($model) use ($building) {
                    $model->where('building_model_id', $building->building_model->id);
                });
            })
                ->where('type', 'standard')
                ->where('name', 'Standard')
                ->first();
            $colors->put($group, $color);
        }

        // replace color for specific building options
        $building->building_options->transform(function($item, $key) use($colors) {
            // search only required options by specific option category [group = siding,trim,roof]
            // skip it
            if (!in_array($item->option->category->group, $colors->keys()->toArray())) {
                return $item;
            }

            // found options - so attach colors to them
            $color = $colors[$item->option->category->group];
            $optionColor = new BuildingOptionColor;
            $optionColor->setRelation('color', $color);
            $item->setRelation('option_color', $optionColor);
            return $item;
        });

        // reset unique serial ID (will be used only plant & model parts)
        $building->serial_number = null;

        $order = new Order();
        $order->sale_type = 'dealer-inventory';
        $order->payment_type = 'rto';

        $order = $this->calculator()
            ->setOrder($order)
            ->setDealer($dealer)
            ->setBuilding($building)
            ->calculateOrder()
            ->calculateRtoAmount()
            ->getOrder();
        $order = $order->present(OrderPdfPresenter::class);

        $pdf = PDF::loadView('forms.inventory', [
            'dealer' => $dealer,
            'order' => $order,
            'orderReference' => new OrderReference,
            'building' => $building,
            'params' => [
                'no_tax' => true
            ],
            'rtoTerms' => \App\Models\Order::$rtoTerms
        ]);
        $pdf->setPaper('Letter');

        return $pdf;
    }

    /**
     * Generate PDF work form for selected order
     * (exclude date and customer information)
     * @param Order $order
     * @return PdfWrapper
     */
    public function getWorkForm(Order $order): PdfWrapper
    {
        $order = $this->calculator()
            ->setOrder($order)
            ->setDealer($order->dealer)
            ->setBuilding($order->building)
            ->calculateOrder()
            ->getOrder();
        $order = $order->present(OrderPdfPresenter::class);
        $order->setRelation('order_reference', new OrderReference());
        $order->sale_type = 'dealer-inventory';
        $order->ced_start = null;
        $order->ced_end = null;

        $pdf = PDF::loadView('forms.work-order', [
            'dealer' => $order->dealer,
            'order' => $order,
            'orderReference' => $order->order_reference,
            'building' => $order->building,
            'params' => [],
            'rtoTerms' => \App\Models\Order::$rtoTerms
        ]);
        $pdf->setPaper('Letter');

        return $pdf;
    }

    /**
     * Generate PDF work building form for selected building-order
     * (exclude order info and customer info, but leave building prices)
     * @param Order $order
     * @param Dealer $dealer
     * @return PdfWrapper
     */
    public function getWorkBuildingForm(Order $order, Dealer $dealer): PdfWrapper
    {
        $order = $this->calculator()
            ->setOrder($order)
            ->setDealer($dealer)
            ->setBuilding($order->building)
            ->calculateOrder()
            ->getOrder();
        $order = $order->present(OrderPdfWorkBuildingFormPresenter::class);
        $order->sale_type = 'dealer-inventory';
        $order->sales_person = null;
        $order->order_date = null;
        $order->payment_type = null;
        $order->payment_method = null;
        $order->rto_type = null;
        $order->rto_term = null;
        $order->ced_start = null;
        $order->ced_end = null;

        $order->delivery_charge = null;
        $order->sales_tax = null;
        $order->total_amount_due = null;
        $order->deposit_amount = null;

        $pdf = PDF::loadView('forms.order', [
            'dealer' => $dealer,
            'order' => $order,
            'orderReference' => new OrderReference,
            'building' => $order->building,
            'params' => [],
            'rtoTerms' => \App\Models\Order::$rtoTerms,
            'sections' => ['order-main', 'order-grid']
        ]);
        $pdf->setPaper('Letter');

        return $pdf;
    }

    /**
     * Generate PDF quote form for /customer-order-form
     * @param Order $order
     * @param array $params
     * @return stdClass
     */
    public function getCustomerForm(Order $order, array $params = []): stdClass
    {
        $order = $this->calculator()
            ->setOrder($order)
            ->setDealer($order->dealer)
            ->setBuilding($order->building)
            ->calculateOrder()
            ->calculateRtoAmount()
            ->getOrder();
        $order = $order->present(OrderPdfCustomerQuotePresenter::class);

        $orderPath = date('Y') . '/' . date('m') . '/';
        $targetFolder = "/public/order/customer/{$orderPath}";
        $filePath = storage_path('app').$targetFolder; // Storage::disk('local')
        $filePrefix = date('Y_m_d_h_i_s');

        $pdfFileName = "{$order->order_reference->last_name}_{$order->order_reference->first_name}_{$filePrefix}.pdf";
        $pdfFileTarget = $filePath . $pdfFileName;

        $pdf = PDF::loadView('forms.quote', [
            'dealer' => $order->dealer,
            'order' => $order,
            'orderReference' => $order->order_reference ?? new OrderReference(),
            'building' => $order->building,
            'params' => [],
            'rtoTerms' => \App\Models\Order::$rtoTerms
        ]);
        $pdf->setPaper('Letter');
        $pdf->save($pdfFileTarget, true);

        $generated = new stdClass();
        $generated->pdf = $pdf;
        $generated->path = $targetFolder.$pdfFileName;

        return $generated;
    }
}
