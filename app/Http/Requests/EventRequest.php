<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
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
            //
            'name'          =>"required|min:2|max:30",
            'time'          =>"required|date_format:h:i A",
            // 'time'          => "required",
            'date'          => "required|date|date_format:Y-m-d",
            'guest_count'   => "required|integer",
            'address_line1' => "required",
            'city'          => "required",
            'state'         => "required",
            'country'       => "required",
            'pincode'       => "required|min:6|max:6",
        ];
    }
}
