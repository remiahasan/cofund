<?php

namespace App\Http\Requests\Campaign;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCampaignRequest extends FormRequest
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
            'category_id' => 'sometimes|exists:categories,id',
            'title' => 'sometimes|string|max:150',
            'description' => 'sometimes|string',
            'target_amount' => 'sometimes|numeric|min:10000',
            'deadline' => 'sometimes|date|after:today',
            'video_url' => 'nullable|url',
            'status' => 'sometimes|in:draft,review,active,success,failed',
        ];
    }
}
