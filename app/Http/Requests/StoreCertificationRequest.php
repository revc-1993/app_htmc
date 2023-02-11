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
            'contract_object' => ['required', 'unique:certifications', 'string', 'min:10', 'max:255'],
            'requesting_area' => ['required', 'max:255'],   // Posiblemente FK
            'reception_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:1'],
            'customer_id' => ['required'],
            // 'certification_number' => ['alpha_dash', 'min:10', 'unique:certifications'],
        ];
    }
}
