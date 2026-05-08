<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'delivery_type' => 'required|in:pickup,courier',
            'description' => 'nullable|string|max:200',

            'delivery_region' => 'required_if:delivery_type,courier|string|max:100',
            'delivery_city' => 'required_if:delivery_type,courier|string|max:100',
            'delivery_street' => 'required_if:delivery_type,courier|string|max:150',
            'delivery_house' => 'required_if:delivery_type,courier|string|max:30',
            'delivery_entrance' => 'required_if:delivery_type,courier|string|max:30',
            'delivery_apartment' => 'required_if:delivery_type,courier|string|max:30',
            'delivery_postal_code' => 'required_if:delivery_type,courier|string|max:20',
        ];
    }
}
