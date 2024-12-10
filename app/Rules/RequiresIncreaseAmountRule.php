<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RequiresIncreaseAmountRule implements ValidationRule
{
    /**
     * Indicates whether the rule should be implicit.
     *
     * @var bool
     */
    public $implicit = true;

    /**
     * @param string  $attribute
     * @param mixed   $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $attributeArrayName = explode(".", $attribute)[0];
        $attributeArrayIndex = explode(".", $attribute)[1];

        // Ugly code but idc, it takes an attribute like "contributions.0.increaseAmount" and fetches "contributions.0" from the request
        // Which in turn gives us the entire array like [ "startAge" => 20, "endAge" => 50, ... "increaseAmount" => 2500, ...]
        $periodicalSettings = request()->input($attributeArrayName . '.' . $attributeArrayIndex);

        if (
            is_null($value) 
            && (!isset($periodicalSettings['frequency']) || $periodicalSettings['frequency'] !== 'One-Off')
            && (!isset($periodicalSettings['increaseFrequency']) || !in_array($periodicalSettings['increaseFrequency'], ['Match Inflation', 'Never']))
        ) {
            $fail(
                $attribute . ' is required if ' .
                $attributeArrayName . '.' . $attributeArrayIndex . '.frequency ' .
                'isn\'t One-Off and ' . 
                $attributeArrayName . '.' . $attributeArrayIndex . '.increaseFrequency ' .
                'isn\'t Match Inflation or Never'
            );
        }
    }
}
