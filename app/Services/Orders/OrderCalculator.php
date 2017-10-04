<?php

namespace App\Services\Orders;

use App\Models\Order;

class OrderCalculator
{

    protected $order;
    protected $dealer;
    protected $building;

    /**
     * OrderCalculator constructor.
     */
    public function __construct(){}

    /**
     * @param Order $model
     * @return OrderCalculator
     */
    public function setOrder(Order $model): OrderCalculator
    {
        $this->order = $model;
        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @return OrderCalculator
     */
    protected function calculateDealer(): OrderCalculator {
        $this->dealer->commission_rate = (float) $this->dealer->commission_rate;
        $this->dealer->tax_rate = (float) $this->dealer->tax_rate;
        $this->dealer->tax_factor = (float) $this->dealer->tax_rate / 100;
        return $this;
    }

    /**
     * Calculate all order properties
     * @return OrderCalculator
     */
    public function calculateOrder(): OrderCalculator {
        // formatting dealer taxes
        $this->calculateDealer();

        $this->calculateTotalSalesPrice();
        $this->calculateSecurityDeposit();
        $this->calculateSalesTax();

        // RTO calculation
        if ($this->order->payment_type === 'rto') {
            $this->calculateNetbuydown();
            $this->calculateRtoAmount();
            $this->calculateRtoPayments($this->order);
            $this->calculateMinDepositAmount();
            $this->calculateDepositAmount();
        }

        // RTO calculation
        if ($this->order->payment_type === 'cash') {
            $this->calculateDepositAmount();
        }

        $this->calculateTotalAmount();
        $this->calculateBalance();

        return $this;
    }

    /**
     * Calculate total sales price
     * @return float
     */
    protected function calculateTotalSalesPrice() {
        $this->order->total_sales_price = $this->building->total_price;
        return $this->order->total_sales_price;
    }

    /**
     * Calculate order security deposit
     */
    protected function calculateSecurityDeposit() {
        $building = $this->building;
        $promo99 = $this->order->promo99;
        $deliveryCharge = $this->order->delivery_charge;
        $taxDeliveryCharge = $this->order->tax_delivery_charge;
        $dealerTaxFactor = (float) $this->dealer->tax_factor;

        if ($promo99)
            $securitDeposit = 99;
        else {
            $width = $building->width;
            if ($width <= 8)                    $securitDeposit = 150;
            if ($width > 8 && $width <= 10)     $securitDeposit = 200;
            if ($width > 10 && $width <= 12)    $securitDeposit = 250;
            if ($width > 12 && $width <= 14)    $securitDeposit = 300;
        }

        $securitDeposit += $deliveryCharge;

        // add tax on delivery fee
        if ($taxDeliveryCharge === true)
            $securitDeposit += $deliveryCharge * $dealerTaxFactor;

        $this->order->security_deposit = (float) number_format($securitDeposit, 2, '.', '');
        return $securitDeposit;
    }

    /**
     * Calculate sales tax
     * @return float
     * @internal param null $totalSalesPrice
     */
    protected function calculateSalesTax() {
        $totalSalesPrice = $this->order->total_sales_price;
        $dealerTaxFactor = (float) $this->dealer->tax_factor;

        $salesTax = $totalSalesPrice * $dealerTaxFactor;

        $this->order->sales_tax = (float) number_format($salesTax, 2, '.', '');
        return $salesTax;
    }

    /**
     * Calculate NetBuydown (used for RTO)
     * @return float
     */
    protected function calculateNetbuydown() {
        $grossBuydown = $this->order->gross_buydown;
        $rtoType = $this->order->rto_type;
        $sercurityDeposit = $this->order->security_deposit;
        $dealerTaxFactor = (float) $this->dealer->tax_factor;
        $rtoNetBuydown = 0;

        if ($rtoType === 'buydown') {
            $netBuydown = ($grossBuydown - $sercurityDeposit);
            $rtoNetBuydown = $netBuydown / (1 + $dealerTaxFactor);
            $buydownTax = $netBuydown - $rtoNetBuydown;

            $this->order->net_buydown = (float) number_format($netBuydown, 2, '.', '');
            $this->order->buydown_tax = (float) number_format($buydownTax, 2, '.', '');
        }

        $this->order->rto_net_buydown = (float) number_format($rtoNetBuydown, 2, '.', '');
        return $rtoNetBuydown;
    }

    /**
     * Calculate RTO Amount
     * @return OrderCalculator
     */
    public function calculateRtoAmount(): OrderCalculator {
        $netBuydown = $this->order->rto_net_buydown;
        $buildingTotal = $this->order->total_sales_price;

        $rtoAmount = $buildingTotal;
        if ($netBuydown > 0) {
            $rtoAmount -= $netBuydown;
        }

        $this->order->rto_amount = (float) number_format($rtoAmount, 2, '.', '');
        return $this;
    }

    /**
     * Calculate RTO Payments
     * @param Order $order
     * @param null $rtoType
     * @return Order $order
     */
    protected function calculateRtoPayments(&$order, $rtoType = null): Order {
        if (!$this->order->rto_term_params) return $order;
        $rtoType = $rtoType ?? $order->rto_type;

        $rtoFactor = (float) $this->order->rto_term_params['rto_factor'];
        $rtoValue = (float) $this->order->rto_term_params['value'];
        $dealerTaxFactor = (float) $this->dealer->tax_factor;

        if ($rtoType === 'no-buydown')
            $rtoAdvanceMonthlyRenewalPayment = $order->total_sales_price / $rtoFactor;
        else
            if ($rtoType === 'buydown')
                $rtoAdvanceMonthlyRenewalPayment = $order->rto_amount / $rtoFactor;
            else
                return $order;

        $rtoSalesTax = $rtoAdvanceMonthlyRenewalPayment * $dealerTaxFactor;
        $rtoTotalAdvanceMonthlyRenewalPayment = $rtoAdvanceMonthlyRenewalPayment + $rtoSalesTax;

        $order->rto_advance_monthly_renewal_payment = (float) number_format($rtoAdvanceMonthlyRenewalPayment, 2, '.', '');
        $order->rto_sales_tax = (float) number_format($rtoSalesTax, 2, '.', '');
        $order->rto_total_advance_monthly_renewal_payment = (float) number_format($rtoTotalAdvanceMonthlyRenewalPayment, 2, '.', '');
        $order->rto_total_days_advance_monthly_renewal_payment = $rtoTotalAdvanceMonthlyRenewalPayment * $rtoValue;
        $order->rto_total_days_advance_monthly_renewal_payment = (float) number_format($order->rto_total_days_advance_monthly_renewal_payment, 2, '.', '');
        return $order;
    }

    /**
     * ONLY for RTO
     * @return float $minDepositAmount
     */
    protected function calculateMinDepositAmount() {
        $promo99 = $this->order->promo99;
        $securityDeposit = $this->order->security_deposit;

        $rtoPayment = 0;
        if (!$promo99) {
            $tmpOrder = $this->order->replicate();
            $tmpOrder = $this->calculateRtoPayments($tmpOrder, 'no-buydown');
            $rtoPayment = $tmpOrder->rto_total_advance_monthly_renewal_payment;
        }

        $minDepositAmount = $securityDeposit + $rtoPayment;

        $this->order->min_deposit_amount = (float) number_format($minDepositAmount, 2, '.', '');
        return $minDepositAmount;
    }

    /**
     * Calculate required deposit amount
     * @return float
     */
    protected function calculateDepositAmount() {
        $paymentType = $this->order->payment_type;
        $deliveryCharge = $this->order->delivery_charge;
        $taxDeliveryCharge = $this->order->tax_delivery_charge;
        $grossBuydown = $this->order->gross_buydown;
        $buildingTotal = $this->order->total_sales_price;
        $dealer = $this->dealer;
        $dealerTaxFactor = (float) $dealer->tax_rate / 100;
        if ($dealer->cash_sale_deposit_rate !== null)
            $cashSaleDepositFactor = $dealer->cash_sale_deposit_rate / 100; else
            $cashSaleDepositFactor = 1;

        $depositAmount = 0;

        if ($paymentType === 'cash') {
            if ($dealer->depositType === 1) {
                $depositAmount = $cashSaleDepositFactor * $buildingTotal;
            } else {
                $depositAmount = $cashSaleDepositFactor * $buildingTotal * (1 + $dealerTaxFactor);

                // add delivery fee with tax
                if ($taxDeliveryCharge === true)
                    $depositAmount += $deliveryCharge * (1 + $dealerTaxFactor);
            }
        }

        if ($paymentType === 'rto') {
            $minDepositAmount = $this->calculateMinDepositAmount();
            // initial deposit amount calculated with grossbuydown = 0
            if ($minDepositAmount > $grossBuydown) {
                $depositAmount = $minDepositAmount + $deliveryCharge;
            } else {
                $depositAmount = $grossBuydown + $deliveryCharge;
            }
        }

        $this->order->deposit_amount = (float) number_format($depositAmount, 2, '.', '');
        return $depositAmount;
    }

    /**
     * @return OrderCalculator
     */
    protected function calculateTotalAmount(): OrderCalculator {
        $deliveryCharge = $this->order->delivery_charge;
        $totalSalesPrice = $this->order->total_sales_price;
        $salesTax = $this->order->sales_tax;

        $totalAmount = $totalSalesPrice + $deliveryCharge + $salesTax;
        $totalAmountDue = $totalSalesPrice + $salesTax;

        $this->order->total_amount = (float) number_format($totalAmount, 2, '.', '');
        $this->order->total_amount_due = (float) number_format($totalAmountDue, 2, '.', '');
        
        return $this;
    }

    /**
     * @return float
     */
    protected function calculateBalance() {
        $totalAmount = $this->order->total_amount;
        $depositReceived = $this->order->deposit_received;

        $balance = $totalAmount - $depositReceived;

        $this->order->balance = (float) number_format($balance, 2, '.', '');
        return $balance;
    }

    /**
     * @param mixed $dealer
     * @return OrderCalculator
     */
    public function setDealer($dealer)
    {
        $this->dealer = $dealer;
        return $this;
    }

    /**
     * @param mixed $building
     * @return OrderCalculator
     */
    public function setBuilding($building)
    {
        $this->building = $building;
        return $this;
    }
}
