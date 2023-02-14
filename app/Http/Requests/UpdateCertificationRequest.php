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
        return [
            'contract_object' => ['required', 'string', 'min:10', 'max:255'],
            'reception_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:1'],
            'department_id' => ['required'],
            'customer_id' => ['required'],

            'certification_number' => ['alpha_dash', 'min:10'],
            'assignment_date' => ['date'],
            'japc_reassignment_date' => ['date'],
            'budget_line' => [''],
            'amount_to_commit' => ['numeric', 'min:1'],
            'obligation_type' => [''],
            'process_type' => [''],
            'comments' => ['max:255'],
            'last_validation' => [''],
        ];
    }
}
