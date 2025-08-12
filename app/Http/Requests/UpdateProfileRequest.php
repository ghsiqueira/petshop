<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
        $user = $this->user();
        
        return [
            // Dados básicos
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            
            // Endereço
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            
            // Redes sociais
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            
            // Configurações
            'preferred_theme' => 'nullable|in:light,dark,auto',
            'preferred_language' => 'nullable|string|max:10',
            
            // Notificações (checkboxes)
            'email_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
            'marketing_emails' => 'nullable|boolean',
            
            // Senha
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|min:8|confirmed',
            'password_confirmation' => 'nullable|string|min:8',
            
            // Upload de foto
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            // Configurações específicas para Cliente
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'veterinarian_name' => 'nullable|string|max:255',
            'veterinarian_phone' => 'nullable|string|max:20',
            'pet_insurance' => 'nullable|string|max:255',
            
            // Configurações específicas para Petshop
            'description' => 'nullable|string|max:2000',
            'opening_hours' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'about' => 'nullable|string|max:2000',
            'mission' => 'nullable|string|max:1000',
            'vision' => 'nullable|string|max:1000',
            'business_license' => 'nullable|string|max:255',
            'years_in_business' => 'nullable|integer|min:0|max:100',
            'delivery_fee' => 'nullable|numeric|min:0',
            'delivery_radius_km' => 'nullable|numeric|min:0',
            'accepts_emergency' => 'nullable|boolean',
            'home_service' => 'nullable|boolean',
            'services_offered' => 'nullable|array',
            'services_offered.*' => 'string|max:255',
            'detailed_hours' => 'nullable|array',
            'certifications' => 'nullable|array',
            'certifications.*' => 'string|max:255',
            'business_type' => 'nullable|string|max:100',
            'specializations' => 'nullable|string|max:500',
            'payment_methods' => 'nullable|string|max:500',
            
            // Configurações específicas para Funcionário
            'specialization' => 'nullable|string|max:255',
            'crmv_number' => 'nullable|string|max:50',
            'work_schedule' => 'nullable|string|max:500',
            'is_veterinarian' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ter um formato válido.',
            'email.unique' => 'Este email já está sendo usado por outro usuário.',
            'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            'bio.max' => 'A biografia não pode ter mais de 1000 caracteres.',
            'birth_date.date' => 'A data de nascimento deve ser uma data válida.',
            'birth_date.before' => 'A data de nascimento deve ser anterior a hoje.',
            'gender.in' => 'O gênero selecionado é inválido.',
            'address.max' => 'O endereço não pode ter mais de 255 caracteres.',
            'city.max' => 'A cidade não pode ter mais de 100 caracteres.',
            'state.max' => 'O estado não pode ter mais de 100 caracteres.',
            'zip_code.max' => 'O CEP não pode ter mais de 20 caracteres.',
            'country.max' => 'O país não pode ter mais de 100 caracteres.',
            'facebook_url.url' => 'A URL do Facebook deve ser válida.',
            'instagram_url.url' => 'A URL do Instagram deve ser válida.',
            'twitter_url.url' => 'A URL do Twitter deve ser válida.',
            'linkedin_url.url' => 'A URL do LinkedIn deve ser válida.',
            'preferred_theme.in' => 'O tema selecionado é inválido.',
            'current_password.required_with' => 'A senha atual é obrigatória para alterar a senha.',
            'password.min' => 'A nova senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'profile_picture.image' => 'O arquivo deve ser uma imagem.',
            'profile_picture.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg, gif.',
            'profile_picture.max' => 'A imagem não pode ser maior que 2MB.',
            'website.url' => 'A URL do website deve ser válida.',
            'years_in_business.integer' => 'Os anos de experiência devem ser um número inteiro.',
            'years_in_business.min' => 'Os anos de experiência não podem ser negativos.',
            'years_in_business.max' => 'Os anos de experiência não podem ser maiores que 100.',
            'delivery_fee.numeric' => 'A taxa de entrega deve ser um número.',
            'delivery_fee.min' => 'A taxa de entrega não pode ser negativa.',
            'delivery_radius_km.numeric' => 'O raio de entrega deve ser um número.',
            'delivery_radius_km.min' => 'O raio de entrega não pode ser negativo.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Verificar senha atual se uma nova senha foi fornecida
            if ($this->filled('password') && $this->filled('current_password')) {
                if (!Hash::check($this->current_password, $this->user()->password)) {
                    $validator->errors()->add('current_password', 'A senha atual está incorreta.');
                }
            }
        });
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'email',
            'phone' => 'telefone',
            'bio' => 'biografia',
            'birth_date' => 'data de nascimento',
            'gender' => 'gênero',
            'address' => 'endereço',
            'city' => 'cidade',
            'state' => 'estado',
            'zip_code' => 'CEP',
            'country' => 'país',
            'facebook_url' => 'URL do Facebook',
            'instagram_url' => 'URL do Instagram',
            'twitter_url' => 'URL do Twitter',
            'linkedin_url' => 'URL do LinkedIn',
            'preferred_theme' => 'tema preferido',
            'preferred_language' => 'idioma preferido',
            'current_password' => 'senha atual',
            'password' => 'nova senha',
            'password_confirmation' => 'confirmação da senha',
            'profile_picture' => 'foto de perfil',
            'emergency_contact' => 'contato de emergência',
            'emergency_phone' => 'telefone de emergência',
            'veterinarian_name' => 'nome do veterinário',
            'veterinarian_phone' => 'telefone do veterinário',
            'pet_insurance' => 'seguro pet',
            'description' => 'descrição',
            'opening_hours' => 'horário de funcionamento',
            'website' => 'website',
            'whatsapp' => 'WhatsApp',
            'about' => 'sobre',
            'mission' => 'missão',
            'vision' => 'visão',
            'business_license' => 'licença comercial',
            'years_in_business' => 'anos de experiência',
            'delivery_fee' => 'taxa de entrega',
            'delivery_radius_km' => 'raio de entrega (km)',
            'specialization' => 'especialização',
            'crmv_number' => 'número do CRMV',
            'work_schedule' => 'horário de trabalho',
        ];
    }
}