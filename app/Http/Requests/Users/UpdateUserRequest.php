<?php

namespace App\Http\Requests\Users;

use Store;
use Entrust;

use App\Models\User;
use App\Http\Requests\Request;
use App\Validators\Validator;

class UpdateUserRequest extends Request
{

    /**
     * Overwrite laravel's method, define custom validator
     */
    public function validate()
    {
        $request = $this->all();
        array_walk_recursive($request, function(&$item, $key) {
            if ($item === 'null') $item = null;
        });
        Request::merge($request);
        $this->validator = Validator::make($request)->addRules(User::$rules);

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
        // TODO: here should be checking for ownership too
        if (!Entrust::hasRole('administrator')) return false;
        
        $itemID = $this->route('user');

        try {
            $item = User::findOrFail($itemID);
            Store::set('user', $item);

            return true;
        } catch (ModelNotFoundException $e) {
            return false;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->validator->append('email', 'email');
        return $this->rules;
    }
}
