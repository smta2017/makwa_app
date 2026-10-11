<?php

namespace App\Http\Requests\Customer;

use App\Lib\ResponseFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class StoreCustomer extends FormRequest
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
            'first_name' => 'required|max:30',
            'last_name' => 'required|max:30',
            'country_code' => 'required',
            // 'phone' => 'required|regex:/^01[0-9]{9}/|min:9',
            'phone' => 'required|unique:customers|regex:/^01[0-9]{9}/|min:9',
            'email' => 'required|unique:customers|max:100',
            'password' => 'required'
        ];
    }

    
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        $responseFormat  = ResponseFormat::error($errors, "invalid validation", JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        throw new HttpResponseException($responseFormat);
    }
}
