<?php

namespace App\Http\Requests;

use App\Exceptions\ApiErrorException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class GetAllFirmsInRadius extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'radius' => 'required|integer',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ApiErrorException('validation', $validator->errors()->toArray(), 422);
    }
}
