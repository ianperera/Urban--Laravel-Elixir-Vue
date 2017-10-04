<?php

namespace App\Jobs\Orders;

use App\Models\FileSign;
use App\Models\Order;
use App\Models\RtoCompany;

use App\Jobs\Job;
use App\Notifications\RtoCompanies\OrderRtoSignatureRequested;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderDocumentsToRtoCompany extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $queue;

    /**
     * @var Order
     */
    public $order;

    /**
     * @var FileSign
     */
    public $fileSign;

    /**
     * Create the job instance.
     * @param Order    $order
     * @param FileSign $fileSign
     */
    public function __construct(Order $order, FileSign $fileSign)
    {
        $this->order = $order;
        $this->fileSign = $fileSign;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $rtoCompany = RtoCompany::first();
        $rtoCompany->notify(new OrderRtoSignatureRequested($this->order, $this->fileSign));
    }
}
