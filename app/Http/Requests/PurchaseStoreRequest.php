<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseStoreRequest extends FormRequest
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
            'rice_item_id' => 'required|exists:rice_items,id',
            'date' => 'required',
            'quantity' => 'required|numeric|min:0',
            'price_per_kg' => 'required|numeric|min:0',
        ];
    }
}
