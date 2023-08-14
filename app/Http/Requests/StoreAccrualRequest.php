<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\FormRequestValidator\Accrual\AccrualRoleValidatorFactory;

class StoreAccrualRequest extends FormRequest
{
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $roleValidator = AccrualRoleValidatorFactory::make($this->getCurrentRole(), $this->input());
        return $roleValidator->getRules();
    }

    protected function getCurrentRole()
    {
        $role = auth()->user()->roles()->first()->id;
        if ($role === 5)    $currentRole = $this->current_management;
        else                $currentRole = $role;
        return $currentRole;
    }
}
