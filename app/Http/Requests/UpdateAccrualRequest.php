<?php

namespace App\Http\Requests;

use App\FormRequestValidator\Accrual\AccrualRoleValidatorFactory;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAccrualRequest extends FormRequest
{
    private $roleValidator;

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
        $this->roleValidator = AccrualRoleValidatorFactory::make($this->getCurrentRole(), $this->input(), $this->route('accrual.id'));
        return $this->roleValidator->getRules();
    }

    public function attributes()
    {
        return $this->roleValidator->getAttributes();
    }

    public function messages()
    {
        return $this->roleValidator->getMessages();
    }

    protected function getCurrentRole()
    {
        $role = auth()->user()->roles->first()->step;
        return $role;
    }
}
