<?php

namespace JohanKladder\Stadia\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DurationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'duration' => 'required|integer',
            'country_id' => 'required_with:climate_code_id|integer|nullable|exists:stadia_countries,id',
            'climate_code_id' => 'sometimes|integer|nullable|exists:climate_codes,id',
        ];
    }
}
