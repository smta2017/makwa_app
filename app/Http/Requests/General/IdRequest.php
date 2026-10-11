<?php

namespace App\Http\Requests\General;

use Illuminate\Foundation\Http\FormRequest;

class IdRequest extends FormRequest
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
            'id' => "required|numeric"
        ];
    }

    public function all($keys = null)
    {
        $request['id'] = $this->route('id');
        return $request;
    }
}
