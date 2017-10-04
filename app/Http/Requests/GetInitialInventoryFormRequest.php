<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Validators\Validator;


class GetInitialInventoryFormRequest extends Request
{
    /**
     * Overwrite laravel's method, define custom validator
     */
    public function validate()
    {
        $req = [
            'inventory_building' => $this->route('inventory_building')
        ];
        $this->validator = Validator::make(Request::all() + $req);

        $this->rules();
        $this->runValidator();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->validator->addRule('inventory_building', 'required|numeric|exists:buildings,id,deleted_at,NULL');
        return $this->rules;
    }
}
