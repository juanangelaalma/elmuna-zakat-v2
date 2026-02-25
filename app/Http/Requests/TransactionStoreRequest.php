<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize input before validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('wa_number')) {
            $this->merge([
                'wa_number' => $this->normalizePhoneNumber($this->input('wa_number')),
            ]);
        }
    }

    /**
     * Normalize phone number to 628... format.
     * Handles: 083..., 6283..., 83...
     */
    private function normalizePhoneNumber(string $phone): string
    {
        // Hapus semua karakter non-digit (spasi, tanda hubung, +, dll)
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            // 08xxx → 628xxx
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            // 8xxx → 628xxx
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'customer' => 'required|string',
            'address' => 'required|string',
            'wa_number' => 'required|string',
            'officer_name' => 'required|string',
            'items' => 'required|array',
        ];
    }
}
