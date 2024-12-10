<?php

namespace App\Rules;

use App\Models\Taxes\TaxSystem;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class IsTaxSystemRule implements ValidationRule
{
    private readonly bool $allowNone;

    public function __construct(private readonly array $options)
    {
        $this->allowNone = $this->options["allowNone"] ?? false;
    }

    /**
     * @param string  $attribute
     * @param mixed   $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $desiredTaxSystem = ucwords($value);

        $supportedTaxSystems = DB::table('tax_systems')->pluck('name')->toArray();
        if ($this->allowNone) {
            array_push($supportedTaxSystems, 'None');
        }

        if (!in_array($desiredTaxSystem, $supportedTaxSystems)) {
            $fail(
                $desiredTaxSystem . 
                ' is not a supported tax system. The supported tax systems are: ' . 
                implode(', ', $supportedTaxSystems)
            );
        }
    }
}
