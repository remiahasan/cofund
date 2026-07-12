<?php

namespace App\Http\Requests\Backing;

use Illuminate\Foundation\Http\FormRequest;

class StoreBackingRequest extends FormRequest
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
            'campaign_id' => 'required|exists:campaigns,id',
            'campaign_tier_id' => 'nullable|exists:campaign_tiers,id',
            'amount' => 'nullable|numeric|min:10000',
        ];
    }
}
