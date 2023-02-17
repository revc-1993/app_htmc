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
        return [
            'process_code' => [],
            'vendor_name' => [],
            'contract_administrator' => [],
            'amount_to_commit' => [],
            'comments' => [],
            'management_status' => [],
            'certification_id' => [],
        ];
    }
}
