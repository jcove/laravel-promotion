<?php

namespace Jcove\Promotion\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterProductPromotion extends FormRequest
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
            'products'          =>  'required|array',
            'promotionId'       =>  'required'
        ];
    }
}
