<?php

namespace App\Rules;

use App\Models\Commitment;
use Illuminate\Contracts\Validation\Rule;

class ValidateAccrualAmount implements Rule
{

    private $commitmentId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($commitmentId)
    {
        $this->commitmentId = $commitmentId;
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
        $commitment = Commitment::findOrFail($this->commitmentId);

        return $value <= $commitment->balance;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El saldo del compromiso seleccionado es insuficiente para el registro del devengado.';
    }
}
