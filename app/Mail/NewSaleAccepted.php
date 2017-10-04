<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Sale;

class NewSaleAccepted extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The dealer and order reference instance.
     *
     * @var Dealer
     * @var Order
     * @var OrderReference
     */
    public $dealer;
    public $order;
    public $orderReference;

    /**
     * Create a new message instance.
     *
     * @param Sale $sale
     */
    public function __construct(Sale $sale)
    {
        $this->order = $sale->order;
        $this->dealer = $sale->order->dealer;
        $this->orderReference = $sale->order_reference;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->to($this->orderReference->email, $this->orderReference->customer_name);
        $this->cc($this->dealer->email, $this->dealer->business_name);
        $this->subject('Your order has been received and processed.');

        if ($this->order->sale_type === 'dealer-inventory') {
            return $this->view('emails.sales.accepted-dealer-inventory');    
        }

        if ($this->order->sale_type === 'custom-order') {
            return $this->view('emails.sales.accepted-custom-order');    
        }
    }
}
