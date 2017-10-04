<?php

namespace App\Validators\Order;

class DealerOrderSubmittedRules {

    /**
     * @param $rules
     * @param $key
     * @param $rule
     */
    private function attach(&$rules, $key, $rule) {
        $rule = is_array($rule) ? $rule : [$rule];

        if (array_key_exists($key, $rules)) {
            $rules[$key] = array_merge($rules[$key], $rule);
        }
    }

    /**
     * @param array $rules
     * @param array $inputs
     * @return DealerOrderSubmittedRules
     */
    public function apply(array &$rules = [], array $inputs = []): DealerOrderSubmittedRules {

        // $rulesDealer
        $rules['id'] = ['uuid', 'required'];

        $this->attach($rules, 'dealer_notes', 'nullable');
        $this->attach($rules, 'note_dealer', 'nullable');

        $this->attach($rules, 'dealer_id', 'required');
        $this->attach($rules, 'sales_person', 'required');

        // $rulesRto
        $this->attach($rules, 'rto_type', 'required_if:payment_type,rto');
        $this->attach($rules, 'rto_term', 'required_if:payment_type,rto');

        $this->attach($rules, 'building_condition', 'required');
        $this->attach($rules, 'sale_type', 'required_if:building_condition,new');
        $this->attach($rules, 'serial', 'required_if:sale_type,dealer-inventory');

        // custom order
        $this->attach($rules, 'building_style', 'required_if:sale_type,custom-order');
        $this->attach($rules, 'building_dimension', 'required_if:sale_type,custom-order');
        $this->attach($rules, 'custom_build_options', 'required_if:sale_type,custom-order');

        // $rulesCustomOrder
        //$this->validator->addRule('custom_build_options', 'nullable|array|is_valid_build_options:empty');
        $this->attach($rules, 'building_package', 'nullable');

        // $rulesCustomer
        $this->attach($rules, 'customer.learning_about_us', 'required');
        $this->attach($rules, 'customer.learning_about_us_other', 'required_if:customer.learning_about_us,other');

        $this->attach($rules, 'customer.first_name', 'required');
        $this->attach($rules, 'customer.last_name', 'required');
        $this->attach($rules, 'customer.email', 'required');
        $this->attach($rules, 'customer.phone_number', 'required');
        $this->attach($rules, 'customer.address', 'required');
        $this->attach($rules, 'customer.city', 'required');
        $this->attach($rules, 'customer.state', 'required');
        $this->attach($rules, 'customer.zip', 'required');
        $this->attach($rules, 'customer.building_location_address', 'required_if:customer.building_in_same_address,0');
        $this->attach($rules, 'customer.building_location_city', 'required_if:customer.building_in_same_address,0');
        $this->attach($rules, 'customer.building_location_state', 'required_if:customer.building_in_same_address,0');
        $this->attach($rules, 'customer.building_location_zip', 'required_if:customer.building_in_same_address,0');

        // $rulesRenter
        $this->attach($rules, 'renter.property_ownership', 'required');

        $this->attach($rules, 'renter.landlord_full_name', 'required_if:renter.property_ownership,rent');
        $this->attach($rules, 'renter.landlord_phone_number', 'required_if:renter.property_ownership,rent');

        $this->attach($rules, 'customer.email', 'required_if:renter.email_instead_of_mail,1');

        $this->attach($rules, 'renter.renter_dob', 'required');
        $this->attach($rules, 'renter.renter_ssn', 'required');
        $this->attach($rules, 'renter.renter_dln', 'required');

        $this->attach($rules, 'renter.co_renter_dob', ['required_with:renter.co_renter_first_name,renter.co_renter_last_name', 'nullable']);
        $this->attach($rules, 'renter.co_renter_ssn', 'required_with:renter.co_renter_first_name,renter.co_renter_last_name');
        $this->attach($rules, 'renter.co_renter_dln', 'required_with:renter.co_renter_first_name,renter.co_renter_last_name');

        $this->attach($rules, 'renter.renter_employer', 'required');
        $this->attach($rules, 'renter.renter_employer_phone_number', 'required');
        $this->attach($rules, 'renter.renter_supervisor', 'required');
        $this->attach($rules, 'renter.renter_supervisor_occupation', 'required');

        $this->attach($rules, 'renter.co_renter_employer', 'required_with:renter.co_renter_first_name,renter.co_renter_last_name');
        $this->attach($rules, 'renter.co_renter_employer_phone_number', 'required_with:renter.co_renter_first_name,renter.co_renter_last_name');
        $this->attach($rules, 'renter.co_renter_supervisor', 'required_with:renter.co_renter_first_name,renter.co_renter_last_name');
        $this->attach($rules, 'renter.co_renter_supervisor_occupation', 'required_with:renter.co_renter_first_name,renter.co_renter_last_name');

        $this->attach($rules, 'renter.reference1_name', 'required');
        $this->attach($rules, 'renter.reference1_relationship', 'required');
        $this->attach($rules, 'renter.reference1_phone_number', 'required');
        $this->attach($rules, 'renter.reference1_address', 'required');
        $this->attach($rules, 'renter.reference1_city', 'required');
        $this->attach($rules, 'renter.reference1_state', 'required');
        $this->attach($rules, 'renter.reference1_zip', 'required');

        $this->attach($rules, 'renter.reference2_name', 'required');
        $this->attach($rules, 'renter.reference2_relationship', 'required');
        $this->attach($rules, 'renter.reference2_phone_number', 'required');
        $this->attach($rules, 'renter.reference2_address', 'required');
        $this->attach($rules, 'renter.reference2_city', 'required');
        $this->attach($rules, 'renter.reference2_state', 'required');
        $this->attach($rules, 'renter.reference2_zip', 'required');

        // $rulesPdfOrder
        $this->attach($rules, 'payment_type', 'required');
        $this->attach($rules, 'payment_method', 'required_with:deposit_received');
        $this->attach($rules, 'order_date', 'required');
        $this->attach($rules, 'ced.start', 'required_with:order_date');
        $this->attach($rules, 'ced.end', 'required_with:order_date');
        $this->attach($rules, 'transaction_id', 'required_if:payment_method,check,credit_card');
        $this->attach($rules, 'delivery_charge', 'required');
        $this->attach($rules, 'gross_buydown', 'required_if:rto_type,buydown');
        $this->attach($rules, 'deposit_received', 'required');

        $this->attach($rules, 'signature_method_id', 'required');

        return $this;
    }
}