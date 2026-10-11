<?php

namespace App\Http\Requests\CustomerAddress;

use App\Lib\ResponseFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class StoreCustomerAddress extends FormRequest
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
            "type" => 'required|string|max:100',
            "location_id" => 'required|numeric|exists:locations,id',
            "latitude" =>  'required',
            "longitude" => 'required',
            "building_no" => 'required|min:1|max:50',
            "street_address" => 'required|max:200',
            "floor_no" => 'max:20',
            "apartment_no" => 'max:20',
            "additional_directions" => 'max:200',
            "is_default" => 'numeric|max:4',
            "country_code" => 'string|max:100',
            "phone_number" => 'string|max:100',
            "landline" => 'string|max:100',
            "building_type" => 'string|max:100',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        $responseFormat  = ResponseFormat::error($errors, "invalid validation", JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        throw new HttpResponseException($responseFormat);
    }
}
