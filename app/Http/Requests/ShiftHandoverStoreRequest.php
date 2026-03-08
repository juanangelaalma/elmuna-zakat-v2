<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShiftHandoverStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'handing_over_officer_name' => ['required', 'string', 'max:255'],
            'receiving_officer_name'    => ['required', 'string', 'max:255'],
            'shift_name'                => ['required', 'string', 'max:255'],
            'transaction_ids'           => ['required', 'array', 'min:1'],
            'transaction_ids.*'         => ['required', 'integer', 'exists:transactions,id'],
        ];
    }
}
