<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\OrderContact;
use App\Models\Setting;
use App\Repositories\OrderContactsRepository;
use Carbon\Carbon;

class OrderContactService
{
    protected $orderContactRepository;

    public function __construct()
    {
        $this->orderContactRepository = new OrderContactsRepository();
    }

    /**
     * @param $data
     * @return OrderContact
     */
    public function store($data): OrderContact
    {
        return $this->orderContactRepository->storeOrderContact($data);
    }

    /**
     * @param OrderContact $orderContact
     * @return OrderContact
     */
    public function save(OrderContact $orderContact): OrderContact
    {
        return $this->orderContactRepository->saveOrderContact($orderContact);
    }

    /**
     * Save order lead contacts - used in dealer order form with sales person
     * @param Order $order
     * @return OrderContact
     */
    public function saveOrderLeadContacts(Order $order): OrderContact
    {
        $orderContact = $this->orderContactRepository->getOrderContactByCustomerId($order->customer_id);
        $initialOrderContactData = [
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'initial_contact' => $order->sales_person,
        ];

        // checking existed order contact: if existed and not older than period - update, if not - add new
        if ($orderContact) {
            $eligibility = Setting::where('id', 'initial_contact_eligibility')->pluck('value')->first();
            $eligibility = !empty($eligibility) ? $eligibility : 0;
            if (!empty($orderContact->order_submit)) {
                return $this->store($initialOrderContactData);
            } else {
                if ($order->status_id == 'submitted') {
                    $orderContact->order_id = $order->id;
                    $orderContact->order_submit = $order->sales_person;
                    return $this->save($orderContact);
                } else {
                    if (Carbon::now() < $orderContact->created_at->addDays($eligibility)) {
                        $orderContact->updated_at = Carbon::now();
                        return $this->save($orderContact);
                    } else {
                        $orderContact->created_at = Carbon::now();
                        $orderContact->initial_contact = $order->sales_person;
                        return $this->save($orderContact);
                    }
                }
            }
        }

        return $this->store($initialOrderContactData);
    }
}
