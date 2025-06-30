<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;

class CekWaktu implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $date = Carbon::parse($value);$hour = $date->hour;
            $minute = $date->minute;

            if ($hour < 18 || ($hour == 18 && $minute < 31)) {
                $fail("it's not too late");
            }
        }catch (\Exception){
            $fail("wrong format");
        }
    }
}
