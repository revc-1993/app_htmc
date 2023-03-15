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
        // dd($this->expense_type_id);

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
                'cgf_comments' => ['nullable'],
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
                'nit_name' => ($this->expense_type_id === 1 ? ['nullable'] : ['required']) + ['string', 'min:15', 'max:255'],
                'budget_line_id' => ['required'],
                'certified_amount' => ['nullable', 'numeric', 'min:10'],
                'certification_number' => ['required_if:record_status,3', 'numeric', 'min:1', 'nullable'],
                'record_status' => ['required', 'in:1,2,3'],
                'certification_comments' => ['nullable'],
            ];
        } else if ($role === 4) {
            $validationRules += [
                'treasury_approved' => ['required'],
                'returned_document_number' => [
                    'required',
                    'regex: /^[A-Z]{4}-[A-Z]{4}-[A-Z]{1,10}-[0-9]{1,4}-[0-9]{2,6}-[MO]$/'
                ],
            ];
        }

        return $validationRules;
    }
}
