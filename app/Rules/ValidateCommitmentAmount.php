<?php

namespace App\Rules;

use App\Models\Certification;
use Illuminate\Contracts\Validation\Rule;

use App\Constants\ManagementRoles;

class ValidateCommitmentAmount implements Rule
{
    private $certificationId;
    private $currentManagement;
    private $treasuryApproved;


    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($certificationId, $currentManagement, $treasuryApproved)
    {
        $this->certificationId = $certificationId;
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
        $certification = Certification::findOrFail($this->certificationId);

        if ($this->treasuryApproved === 'returned' && $this->currentManagement === ManagementRoles::COORD_CGF)
            return true;

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
