<?php

namespace App\Rules;

use App\Constants\ManagementRoles;
use App\Constants\ManagementRecordStatus;
use Illuminate\Contracts\Validation\Rule;

class CustomRecordStatusRule implements Rule
{

    private $currentManagement;
    private $treasuryApproved;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($currentManagement, $treasuryApproved)
    {
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
        if (
            $this->currentManagement === ManagementRoles::COORD_CGF &&
            ($this->treasuryApproved === 'returned'
                || $this->treasuryApproved === 'canceled'
                || $this->treasuryApproved === 'liquidated'
            )
        ) {
            return true;
        }

        return !empty($value) && in_array($value, [
            ManagementRecordStatus::NOT_REVIEWED,
            ManagementRecordStatus::IN_REVIEW,
            ManagementRecordStatus::OBSERVED,
            ManagementRecordStatus::RETURNED,
            ManagementRecordStatus::REGISTERED,
        ]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El campo Estado de registro debe tener una opción válida.';
    }
}
