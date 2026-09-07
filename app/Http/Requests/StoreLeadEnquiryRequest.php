<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+()\-\s]+$/'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'company' => ['nullable', 'prohibited'],
        ];
    }
}
