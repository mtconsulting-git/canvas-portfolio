<?php

namespace Canvas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranslationRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'language' => 'required|string',
            'name' => 'nullable|string',
            'title' => 'nullable|string',
            'summary' => 'nullable|string',
            'body' => 'nullable|string',
            'info' => 'nullable|array',
            'meta' => 'nullable|array',
        ];
    }
}
