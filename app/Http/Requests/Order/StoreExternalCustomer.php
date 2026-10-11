<?php

namespace App\Http\Requests\Order;

use App\Lib\ResponseFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class StoreExternalCustomer extends FormRequest
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
            'phone' => 'required|unique:customers|max:20',
            'email' => 'required|unique:customers|max:100',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        $responseFormat  = ResponseFormat::error($errors, "invalid validation", JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        throw new HttpResponseException($responseFormat);
    }
}
