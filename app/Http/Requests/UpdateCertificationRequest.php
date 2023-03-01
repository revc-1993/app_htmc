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
            'certification_memo' => ['nullable', 'alpha_dash', 'min:20', 'max:40'],
            'content' => ['nullable', 'min:10'],
            'contract_object' => ['required', 'string', 'min:15', 'max:255'],
            'process_type_id' => ['required'],
            'expense_type_id' => ['required'],
            'department_id' => ['required'],
            'cgf_comments' => ['nullable'],

            'japc_comments' => ['nullable'],
            'customer_id' => ['nullable'],

            // 'certification_number' => ['nullable', 'alpha_dash', 'min:10'],
            // 'assignment_date' => ['nullable', 'date', 'after_or_equal:reception_date', 'before_or_equal:today'],
            // 'japc_reassignment_date' => ['nullable', 'date', 'after_or_equal:assignment_date', 'before_or_equal:today'],
            // 'budget_line' => ['nullable'],
            // 'amount_to_commit' => ['nullable', 'numeric', 'min:10'],
            // 'obligation_type' => ['nullable'],
            // 'process_type' => ['nullable'],
            // 'comments' => ['nullable', 'max:255'],
            // 'last_validation' => ['nullable'],
        ];
    }
}
