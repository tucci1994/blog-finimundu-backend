<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'          => ['sometimes', 'string', 'max:255'],
            'excerpt'        => ['sometimes', 'nullable', 'string', 'max:500'],
            'content'        => ['sometimes', 'string'],
            'featured_image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status'         => ['sometimes', Rule::in(['draft', 'published'])],
        ];
    }

    public function messages(): array
    {
        return [
            'title.max'            => 'Il titolo non puo\' superare 255 caratteri.',
            'status.in'            => 'Lo stato deve essere "draft" o "published".',
            'featured_image.image' => "Il file deve essere un'immagine.",
            'featured_image.max'   => "L'immagine non puo' superare 2MB.",
        ];
    }
}
