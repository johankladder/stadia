<?php

namespace JohanKladder\Stadia\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalendarRangeRequest extends FormRequest
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
            'range_from' => 'required|date',
            'range_to' => 'required|date',
            'country_id' => 'required_with:climate_code_id|integer|nullable|exists:countries,id',
            'climate_code_id' => 'sometimes|integer|nullable|exists:climate_codes,id',
        ];
    }
}
