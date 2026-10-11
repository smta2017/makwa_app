<?php

namespace App\Http\Requests\Customer;

use App\Http\Resources\V1\Customer\Customer;
use App\Lib\ResponseFormat;
use App\Repositories\Customer\CustomerRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AffiliateStoreCustomer extends FormRequest
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
            'email' => 'required|max:100'
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        // use trans instead on Lang 
        return [
            'phone.unique' => [
                'msg' => 'Duplicate phone number',
                'customer' => new Customer(CustomerRepository::GetCustomerByPhone($this->input('phone'))),
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        $responseFormat  = ResponseFormat::error($errors, "invalid validation", JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        throw new HttpResponseException($responseFormat);
    }

    // protected function failedValidation(Validator $validator)
    // {

    //     $customer = new Customer(CustomerRepository::GetCustomerByPhone($this->input('phone')));

    //     // need to pass Resource Directly
    //     $response =  RB::error('100', null, [
    //         "customer" => [
    //             'id' => $customer["id"],
    //             'first_name' => $customer["first_name"],
    //             'last_name' => $customer["last_name"],
    //             'phone' => $customer["phone"],
    //             'email' => $customer["email"],
    //         ]
    //     ]);

    //     throw (new ValidationException($validator, $response));
    // }
}
