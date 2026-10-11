<?php

namespace App\Http\Requests\Order;

use App\Lib\ResponseFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class StoreOrder extends FormRequest
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
            // 'customer_id' => 'required|numeric',
            'address_id' => 'required|numeric',
            'method_of_payment' => 'required|string|max:50',
            'pickup_date' => 'required|date_format:Y-m-d',
            'pickup_start' => 'required|date_format:H:i:s',
            'pickup_end' => 'required|date_format:H:i:s',
            'delivery_date' => 'required|date_format:Y-m-d',
            'delivery_start' => 'required|date_format:H:i:s',
            'delivery_end' => 'required|date_format:H:i:s',
            'promo_code' => 'max:20',
            'customer_notes' => 'string|max:255',
            'selected_card' => 'string|max:255',
            // 'customised_preference' => 'required|numeric|between:0,9',
            'vendor_id' => 'numeric|max:9999',
            'service_id' => 'numeric|max:10',
            // 'preferences' => 'max:20',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        $responseFormat  = ResponseFormat::error($errors, "invalid validation", JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        throw new HttpResponseException($responseFormat);
    }
}