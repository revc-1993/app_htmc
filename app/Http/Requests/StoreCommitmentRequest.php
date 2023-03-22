<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommitmentRequest extends FormRequest
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
            'certification_id' => ['required'],
            'commitment_memo' => [
                'nullable',
                'regex: /^[A-Z]{4}-[A-Z]{4}-[A-Z]{1,10}-[0-9]{1,4}-[0-9]{2,6}-[MO]$/'
            ],
            'process_number' => ['nullable', 'alphadash', 'string', 'min:15', 'max:100'],
            'contract_administrator' => ['required'],
            'customer_id' => ['required'],
        ];
    }
}
