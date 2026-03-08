<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'excerpt'        => ['nullable', 'string', 'max:500'],
            'content'        => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status'         => ['required', Rule::in(['draft', 'published'])],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'Il titolo e\' obbligatorio.',
            'content.required'     => 'Il contenuto e\' obbligatorio.',
            'status.required'      => 'Lo stato e\' obbligatorio.',
            'status.in'            => 'Lo stato deve essere "draft" o "published".',
            'featured_image.image' => "Il file deve essere un'immagine.",
            'featured_image.max'   => "L'immagine non puo' superare 2MB.",
        ];
    }
}
