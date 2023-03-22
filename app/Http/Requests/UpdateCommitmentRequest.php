<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommitmentRequest extends FormRequest
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

        if ($role === 2) {
            $validationRules += [
                'certification_id' => ['required'],
                'commitment_memo' => [
                    'nullable',
                    'regex: /^[A-Z]{4}-[A-Z]{4}-[A-Z]{1,10}-[0-9]{1,4}-[0-9]{2,6}-[MO]$/'
                ],
                'process_number' => ['nullable', 'alphadash', 'string', 'min:15', 'max:100'],
                'contract_administrator' => ['required'],
                'customer_id' => ['required'],
            ];
        } else if ($role === 3) {
            $validationRules += [
                'vendor_id' => ['nullable'],
                'commitment_amount' => ['nullable', 'numeric', 'min:10'],
                'record_status_id' => ['required', 'in:1,2,3,4'],
                'commitment_cur' => ['required_if:record_status_id,4', 'numeric', 'min:1', 'nullable'],
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
}
