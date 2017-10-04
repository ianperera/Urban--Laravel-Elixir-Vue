<?php

namespace App\Services\Customers;

use App\Exceptions\GeneralException;
use App\Models\User;
use App\Models\Order;
use App\Models\Role;

class CustomerService
{
    public function __construct()
    {
    }

    /**
     * Add new user (customer) or attach role 'customer' to existed
     * @param Order $order
     * @return User
     * @throws GeneralException
     */
    public function createCustomerFromOrder(Order $order): User
    {
        $user = User::firstOrCreate([
            'email' => $order->order_reference->email
        ], [
            'first_name' => $order->order_reference->first_name,
            'last_name' => $order->order_reference->last_name,
            'password' => bcrypt(str_random(8))
        ]);

        try {
            if (!$user->hasRole('customer')) {
                $customerRole = Role::where('name', 'customer')->firstOrFail();
                $user->save();
                $user->roles()->attach($customerRole);
            }
        } catch (Exception $e) {
            throw new GeneralException(trans('exceptions.customers.unable_to_save_customer_from_order'));
        }

        return $user;
    }
}
