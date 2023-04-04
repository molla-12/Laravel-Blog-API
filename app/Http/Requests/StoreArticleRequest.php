<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'category_id' => ['required', 'integer', Rule::In('categories')],
            'title' => ['required', 'string'],
            'tag.*' => ['nullable', Rule::In('tags')],
            'details' => ['required', 'string'],
            'image.*' => ['nullable', 'jpeg', 'jpg', 'png', 'gif'],
        ];
    }
}