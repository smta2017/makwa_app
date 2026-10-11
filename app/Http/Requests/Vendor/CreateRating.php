<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class CreateRating extends FormRequest
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
            'title' => 'string|max:255',
            'body' => 'string|max:300',
            'customer_service_rating' => 'numeric|max:5',
            'quality_rating' =>  'numeric|max:5',
            'friendly_rating' =>  'numeric|max:5',
            'pricing_rating' =>  'numeric|max:5',
            'rating' =>  'numeric|max:5',
            // 'recommend' => 're',
            'delivery_time_rating' => 'numeric|max:5',
            'order_packaging_rating' => 'numeric|max:5',
            'value_money_rating' => 'numeric|max:5',
        ];
    }
}
