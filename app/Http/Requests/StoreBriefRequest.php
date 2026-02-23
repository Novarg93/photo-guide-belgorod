<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBriefRequest extends FormRequest
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
            'category_slug' => ['required', 'string', 'exists:categories,slug'],
            'mood' => ['nullable', 'string'],
            'season' => ['nullable', 'string'],
            'location' => ['nullable', 'string'],
            'clothing' => ['nullable', 'string'],
            'active_filter_option_keys' => ['nullable', 'array'],
            'active_filter_option_keys.*' => ['string'],
            'people_count' => ['nullable', 'string', 'in:1,2,3-4,5+'],
            'notes' => ['nullable', 'string'],
            'retouch_preference' => ['nullable', 'string'],
            'color_style' => ['nullable', 'string'],
            'selected_example_ids' => ['nullable', 'array'],
            'selected_example_ids.*' => ['integer', 'exists:examples,id'],
            'selected_photo_ids' => ['nullable', 'array'],
            'selected_photo_ids.*' => ['integer', 'exists:photos,id'],
        ];
    }
}
