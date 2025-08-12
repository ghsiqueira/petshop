<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadPhotoRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'photo' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120', // 5MB max
                'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'photo.required' => 'É necessário selecionar uma foto.',
            'photo.image' => 'O arquivo deve ser uma imagem válida.',
            'photo.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg, gif ou webp.',
            'photo.max' => 'A imagem não pode ser maior que 5MB.',
            'photo.dimensions' => 'A imagem deve ter pelo menos 100x100 pixels e no máximo 4000x4000 pixels.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'photo' => 'foto',
        ];
    }
}