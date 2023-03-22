<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCertificationRequest extends FormRequest
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
        $role = auth()->user()->roles()->first()->id;
        $validationRules = [];

        if ($role === 1) {
            $validationRules += [
                'certification_memo' => [
                    'nullable',
                    'regex: /^[A-Z]{4}-[A-Z]{4}-[A-Z]{1,10}-[0-9]{1,4}-[0-9]{2,6}-[MO]$/'
                ],
                'content' => ['nullable', 'min:8'],
                'contract_object' => ['required', 'string', 'min:15', 'max:255'],
                'process_type_id' => ['required'],
                'expense_type_id' => ['required'],
                'department_id' => ['required'],
                'sec_cgf_comments' => ['nullable'],
            ];
        } else if ($role === 2) {
            $validationRules += [
                'content' => ['nullable', 'min:10'],
                'japc_comments' => ['nullable'],
                'customer_id' => ['required'],
            ];
        } else if ($role === 3) {
            $validationRules += [
                'process_number' => ($this->expense_type_id === 1 ? ['nullable'] : ['required']) + ['alphadash', 'string', 'min:15', 'max:100'],
                'vendor_id' => ($this->expense_type_id === 1 ? ['nullable'] : ['required']),
                'budget_line_id' => ['required'],
                'certified_amount' => ['nullable', 'numeric', 'min:10'],
                'certification_number' => ['required_if:record_status_id,4', 'numeric', 'min:1', 'nullable'],
                'record_status_id' => ['required', 'in:1,2,3,4'],
                'certification_comments' => ['nullable'],
            ];
        } else if ($role === 4) {
            $validationRules += [
                'treasury_approved' => ['required'],
                'returned_document_number' => [
                    'required',
                    'regex: /^[A-Z]{4}-[A-Z]{4}-[A-Z]{1,10}-[0-9]{1,4}-[0-9]{2,6}-[MO]$/',
                ],
                'coord_cgf_comments' => ['nullable'],
            ];
        }

        return $validationRules;
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
}
