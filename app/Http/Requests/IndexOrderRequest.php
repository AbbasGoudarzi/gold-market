<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexOrderRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'in:' . OrderType::valuesInString(),
            'statuses' => 'array',
            'statuses.*' => 'in:' . OrderStatus::valuesInString(),
        ];
    }
}
