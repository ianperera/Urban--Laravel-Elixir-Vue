<?php

namespace App\Services\Orders;

use App\Models\Location;
use App\Models\Order;

use App\Events\Orders\OrderWasUpdated;
use App\Events\Orders\OrderCustomerWasUpdated;

use App\Services\Locations\LocationService;
use Auth;
use Event;
use DB;
use Exception;
use Uuid;
use PDF;
use Storage;
use Store;
use Helper;

use Carbon\Carbon;

class OrderService
{
    public function __construct()
    {
    }

    /**
     * @param Order|null $order
     * @return Order
     */
    public function saveDealerOrder(Order $order = null): Order
    {
        DB::transaction(function () use (&$order) {

            if ($order->status_id == 'submitted') {
                if ($order->sale !== null && $order->sale->status_id == 'invoiced') {
                    $order->sale->status_id = 'updated';
                    $order->sale->save();
                }
                $order->date_submitted = Carbon::now();
            }

            if (!$order->exists) {
                $order->save();
            }

            // update building if provided
            if ($order->relationLoaded('building')) {
                $building = $order->building;
                $building->order_id = $order->id;
                $building->save();

                if ($order->sale_type !== 'dealer-inventory') {
                    $building->building_options()->delete();
                    $building->building_options->each(function ($buildingOption) use ($building) {
                        $buildingOption->building_id = $building->id;
                        $buildingOption->save();
                        if ($buildingOption->relationLoaded('option_color') && $buildingOption->option_color) {
                            $buildingOption->option_color()->save($buildingOption->option_color);
                        }
                    });
                }

                $order->building_id = $building->id;
            }

            // update order reference if provided
            if ($order->relationLoaded('order_reference')) {
                // create new customer if email provided and it is not dealer's email
                if ($order->order_reference->isDirty('email') && $order->dealer->email !== $order->order_reference->email) {
                    event(new OrderCustomerWasUpdated($order));
                }

                $order->order_reference->save();
                $order->reference_id = $order->order_reference->id;
            }

            $order->save();

            $orderSericeContact = new OrderContactService;
            $orderSericeContact->saveOrderLeadContacts($order);

            event(new OrderWasUpdated($order));
        });

        return $order;
    }

    /**
     * @param $order
     * @return Location
     */
    public function generateLocation(&$order): Location
    {
        $locationParams = [];

        $refereces = $order->order_reference;
        if ($refereces->building_in_same_address) {
            $locationParams['address'] = $refereces->address;
            $locationParams['city'] = $refereces->city;
            $locationParams['state'] = $refereces->state;
            $locationParams['zip'] = $refereces->zip;
        } else {
            $locationParams['address'] = $refereces->building_location_address;
            $locationParams['city'] = $refereces->building_location_city;
            $locationParams['state'] = $refereces->building_location_state;
            $locationParams['zip'] = $refereces->building_location_zip;
        }

        $locationParams['name'] = "{$order->order_reference->first_name} {$order->order_reference->last_name} ({$order->building->serial_number})";
        $locationParams['country'] = 'US';
        $locationParams['latitude'] = null;
        $locationParams['longitude'] = null;
        $locationParams['is_geocoded'] = 'no';
        $locationParams['category'] = Location::CATEGORY_CUSTOMER;

        $location = (new LocationService)->create($locationParams);
        return $location;
    }
}
