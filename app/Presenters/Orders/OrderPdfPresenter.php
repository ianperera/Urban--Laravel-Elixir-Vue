<?php
namespace App\Presenters\Orders;

use Hemp\Presenter\Presenter;
use Carbon\Carbon;

class OrderPdfPresenter extends Presenter
{
    public function getOrderDateAttribute(){
        return Carbon::createFromFormat('Y-m-d', $this->model->order_date)->format('m/d/Y');
    }

    public function getCedStartAttribute(){
        return Carbon::createFromFormat('Y-m-d', $this->model->ced_start)->format('m/d/Y');
    }

    public function getCedEndAttribute(){
        return Carbon::createFromFormat('Y-m-d', $this->model->ced_end)->format('m/d/Y');
    }

    public function getTxtGrossBuydownAttribute(){
        return '$' . number_format($this->gross_buydown, 2);
    }

    public function getTxtTotalSalesPriceAttribute(){
        return '$' . number_format($this->total_sales_price, 2);
    }

    public function getTxtSalesTaxRateAttribute(){
        return number_format($this->dealer->tax_rate, 2) . '%';
    }

    public function getTxtSalesTaxAttribute(){
        return '$' . number_format($this->sales_tax, 2);
    }

    public function getTxtSecurityDepositAttribute(){
        return '$' . number_format($this->security_deposit, 2);
    }

    public function getTxtTotalAmountAttribute(){
        return '$' . number_format($this->total_amount, 2);
    }

    public function getTxtTotalAmountDueAttribute(){
        return '$' . number_format($this->total_amount_due, 2);
    }

    public function getTxtBalanceAttribute(){
        return '$' . number_format($this->balance, 2);
    }

    public function getTxtDepositAmountAttribute(){
        return '$' . number_format($this->deposit_amount, 2);
    }

    public function getTxtDepositReceivedAttribute(){
        return '$' . number_format($this->deposit_received, 2);
    }

    public function getTxtDeliveryChargeAttribute(){
        return '$' . number_format($this->delivery_charge, 2);
    }

    public function getTxtNetBuydownAttribute(){
        return '$' . number_format($this->net_buydown, 2);
    }

    public function getTxtBuydownTaxAttribute(){
        return '$' . number_format($this->buydown_tax, 2);
    }

    public function getTxtRtoNetBuydownAttribute(){
        return '$' . number_format($this->rto_net_buydown, 2);
    }

    public function getTxtRtoSalesTaxAttribute(){
        return '$' . number_format($this->rto_sales_tax, 2);
    }

    public function getTxtRtoAmountAttribute(){
        return '$' . number_format($this->rto_amount, 2);
    }

    public function getTxtRtoAdvanceMonthlyRenewalPaymentAttribute(){
        return '$' . number_format($this->rto_advance_monthly_renewal_payment, 2);
    }

    public function getTxtRtoTotalAdvanceMonthlyRenewalPaymentAttribute(){
        return '$' . number_format($this->rto_total_advance_monthly_renewal_payment, 2);
    }

    public function getTxtRtoTotalDaysAdvanceMonthlyRenewalPaymentAttribute(){
        return '$' . number_format($this->rto_total_days_advance_monthly_renewal_payment, 2);
    }
}