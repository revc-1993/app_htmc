<?php

namespace App\Rules;

use App\Models\Commitment;
use App\Constants\ManagementRoles;
use Illuminate\Contracts\Validation\Rule;

class ValidateAccrualAmount implements Rule
{

    private $commitmentId;
    private $currentManagement;
    private $treasuryApproved;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($commitmentId, $currentManagement, $treasuryApproved)
    {
        $this->commitmentId = $commitmentId;
        $this->currentManagement = $currentManagement;
        $this->treasuryApproved = $treasuryApproved;
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

        if ($this->treasuryApproved === 'returned' && $this->currentManagement === ManagementRoles::COORD_CGF)
            return true;

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
