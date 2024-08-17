<?php

namespace App\Http\Requests\Fire;

use App\Http\Requests\APIFormRequest;

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
            'age'  => 'required|integer|min:0|max:119',
            'endAge' => 'required|integer|min:1|max:120',
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
            // 'age.required' => 'An age is required',
            // 'endAge.required' => 'An end age is required',
        ];
    }
}