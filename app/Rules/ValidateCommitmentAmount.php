<?php

namespace App\Rules;

use App\Models\Certification;
use Illuminate\Contracts\Validation\Rule;

class ValidateCommitmentAmount implements Rule
{
    private $certificationId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($certificationId)
    {
        $this->certificationId = $certificationId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $certification = Certification::findOrFail($this->certificationId);

        return $value <= $certification->balance;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El saldo de la certificación seleccionada es insuficiente para el registro del compromiso.';
    }
}
