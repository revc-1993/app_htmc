<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'certification_memo' => ['nullable', 'alpha_dash', 'min:20', 'max:40'],
            'content' => ['nullable', 'min:10'],
            'contract_object' => ['required', 'string', 'min:15', 'max:255'],
            'process_type_id' => ['required'],
            'expense_type_id' => ['required'],
            'department_id' => ['required'],
            'cgf_comments' => ['nullable'],

            // 'reception_date' => ['required', 'date', 'before_or_equal:today', 'after:last year'],
            // 'amount' => ['required', 'numeric', 'min:10'],
            // 'customer_id' => ['required'],
            // 'certification_number' => ['alpha_dash', 'min:10', 'unique:certifications'],
        ];
    }
}
