<?php
namespace App\Presenters\Orders;

use Hemp\Presenter\Presenter;

class OrderPdfCustomerQuotePresenter extends Presenter
{
    public function getTxtGrossBuydownAttribute(){
        return '';
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

    public function getTxtTotalAmountDueAttribute(){
        return '$' . number_format($this->total_amount_due, 2);
    }

    public function getTxtDepositAmountAttribute(){
        return '';
    }

    public function getTxtDepositReceivedAttribute(){
        return '';
    }

    public function getTxtDeliveryChargeAttribute(){
        return '';
    }

    public function getTxtNetBuydownAttribute(){
        return '';
    }

    public function getTxtBuydownTaxAttribute(){
        return '';
    }

    public function getTxtRtoNetBuydownAttribute(){
        return '';
    }

    public function getTxtRtoSalesTaxAttribute(){
        return '$' . number_format($this->rto_sales_tax, 2);
    }

    public function getTxtRtoAmountAttribute(){
        return '$' . number_format($this->rto_amount, 2);
    }

    public function getTxtRtoAdvanceMonthlyRenewalPaymentAttribute(){
        return '';
    }

    public function getTxtRtoTotalAdvanceMonthlyRenewalPaymentAttribute(){
        return '';
    }

    public function getTxtRtoTotalDaysAdvanceMonthlyRenewalPaymentAttribute(){
        return '';
    }
}