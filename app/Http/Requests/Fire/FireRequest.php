<?php

namespace App\Http\Requests\Fire;

use App\Http\Requests\APIFormRequest;
use App\Rules\IsTaxSystemRule;
use App\Rules\RequiresIncreaseAmountRule;

class FireRequest extends APIFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'startAge'           => 'required|integer|min:0|max:119',
            'endAge'             => 'required|integer|min:1|max:120',
            'useRealInflation'   => 'required|boolean',
            'staticInflation'    => 'required_if:realInflation,false|decimal:0,2|min:0|max:10',
            'flatReturns'        => 'decimal:0,2|min:0.1|max:50',
            'taxSystem'          => ['required', 'string', 'bail', new IsTaxSystemRule(['allowNone' => true])],
            'dataSince'          => 'required|integer|min:1871',
            'startBalance'       => 'required|integer|min:0|max_digits:15',

            'contributions' => 'required|array|min:1',

            'contributions.*.startAge'          => 'required|integer|min:0|max:119',
            'contributions.*.endAge'            => 'required_unless:contributions.*.frequency,One-Off|integer|min:1|max:120|exclude_if:contributions.*.frequency,One-Off',
            'contributions.*.amount'            => 'required|decimal:0,2|min:1|max:1000000000000', // max 1 trillion because max_digits: doesnt work with decimals
            'contributions.*.frequency'         => 'required|string|in:One-Off,Monthly,Quarterly,Yearly',
            'contributions.*.increaseFrequency' => 'required_unless:contributions.*.frequency,One-Off|string|in:Never,Monthly,Quarterly,Yearly,Match Inflation|exclude_if:contributions.*.frequency,One-Off',
            'contributions.*.increaseAmount'    => [new RequiresIncreaseAmountRule(), 'exclude_if:contributions.*.frequency,One-Off', 'exclude_if:contributions.*.increaseFrequency,Never', 'exclude_if:contributions.*.increaseFrequency,Match Inflation'],

            'withdrawals' => 'sometimes|array|min:1',

            'withdrawals.*.startAge'          => 'required|integer|min:0|max:119',
            'withdrawals.*.endAge'            => 'required_unless:withdrawals.*.frequency,One-Off|integer|min:1|max:120|exclude_if:withdrawals.*.frequency,One-Off',
            'withdrawals.*.amount'            => 'required|decimal:0,2|min:1|max:1000000000000', // max 1 trillion because max_digits: doesnt work with decimals
            'withdrawals.*.frequency'         => 'required|string|in:One-Off,Monthly,Quarterly,Yearly',
            'withdrawals.*.increaseFrequency' => 'required_unless:withdrawals.*.frequency,One-Off|string|in:Never,Monthly,Quarterly,Yearly,Match Inflation|exclude_if:withdrawals.*.frequency,One-Off',
            'withdrawals.*.increaseAmount'    => [new RequiresIncreaseAmountRule(), 'exclude_if:withdrawals.*.frequency,One-Off', 'exclude_if:withdrawals.*.increaseFrequency,Never', 'exclude_if:withdrawals.*.increaseFrequency,Match Inflation']
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'contributions.required' => 'Contributions is required and must have at least 1 item',
            'contributions.*.frequency' => 'frequency is required and must be one of: One-Off, Monthly, Quarterly or Yearly',
            'contributions.*.increaseFrequency' => 'contributions.:index.increaseFrequency is required if contributions.:index.frequency isn\'t One-Off and must be one of: Never, Monthly, Quarterly, Yearly or Match Inflation',

            'withdrawals.*.frequency' => 'frequency is required and must be one of: One-Off, Monthly, Quarterly or Yearly',
            'withdrawals.*.increaseFrequency' => 'withdrawals.:index.increaseFrequency is required if withdrawals.:index.frequency isn\'t One-Off and must be one of: Never, Monthly, Quarterly, Yearly or Match Inflation'
        ];
    }
}