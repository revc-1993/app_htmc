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
            'certification_memo' => [
                'nullable',
                'regex: /^[A-Z]{4}-[A-Z]{4}-[A-Z]{1,10}-[0-9]{1,4}-[0-9]{2,6}-[MO]$/'
            ],
            'content' => ['nullable', 'min:10'],
            'contract_object' => ['required', 'string', 'min:15', 'max:255'],
            'process_type_id' => ['required'],
            'expense_type_id' => ['required'],
            'department_id' => ['required'],
            'sec_cgf_comments' => ['nullable'],
        ];
    }
}
