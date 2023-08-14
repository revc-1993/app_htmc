<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\FormRequestValidator\Certification\CertificationRoleValidatorFactory;

class StoreCertificationRequest extends FormRequest
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
        $roleValidator = CertificationRoleValidatorFactory::make($this->getCurrentRole(), $this->input());

        return $roleValidator->getRules();
    }

    public function messages(): array
    {
        return [
            // 'name.required' => __('El campo nombre es obligatorio.'),
            // 'name.string' => __('El campo nombre debe ser una cadena de texto.'),
            // 'name.max' => __('El campo nombre no debe ser mayor a :max caracteres.'),
            // 'name.unique' => __('El campo nombre ya está en uso.'),
            // 'description.required' => __('El campo descripción es obligatorio.'),
            // 'description.string' => __('El campo descripción debe ser una cadena de texto.'),
            // 'description.max' => __('El campo descripción no debe ser mayor a :max caracteres.'),
        ];
    }

    protected function getCurrentRole()
    {
        $role = auth()->user()->roles()->first()->id;
        if ($role === 5)    $currentRole = $this->current_management;
        else                $currentRole = $role;
        return $currentRole;
    }
}
