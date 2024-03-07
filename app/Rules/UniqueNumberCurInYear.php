<?php

namespace App\Rules;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\Rule;

class UniqueNumberCurInYear implements Rule
{
    private $module;
    private $currentRecordId;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(String $module, int $currentRecordId)
    {
        $this->module = $module;
        $this->currentRecordId = $currentRecordId;
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
        $currentYear = date('Y');

        $count = DB::table($this->module . "s")
            ->whereYear('created_at', $currentYear)
            ->where($this->getFieldName(), $value)
            ->where('id', '!=', $this->currentRecordId)
            ->count();

        return $count === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Este número de CUR ya existe en los registros del año actual.';
    }

    private function getFieldName()
    {
        switch ($this->module) {
            case 'certification':
                return 'certification_number';
            case 'commitment':
                return 'commitment_cur';
            case 'accrual':
                return 'accrual_cur';
                // default:
                //     return 'numero';
        }
    }
}
