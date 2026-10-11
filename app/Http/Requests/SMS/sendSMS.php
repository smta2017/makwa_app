<?php

namespace App\Http\Requests\SMS;

use Illuminate\Foundation\Http\FormRequest;
use App\Lib\ResponseFormat;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class sendSMS extends FormRequest
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
            "phone" => "required",
            "verification_code" => "max:9999999",
            "customer_id" => "numeric",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        $responseFormat  = ResponseFormat::error($errors, "invalid validation", JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        throw new HttpResponseException($responseFormat);
    }
}
