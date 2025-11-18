<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Libraries\ApiResponse;

class UtilisateurRequest extends FormRequest
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
        $rules = [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'service' => 'required|string|max:255',
            'badge_id' => 'required|integer|exists:badges,id',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = [
                'nom' => 'sometimes|string|max:255',
                'prenom' => 'sometimes|string|max:255',
                'service' => 'sometimes|string|max:255',
                'badge_id' => 'sometimes|integer|exists:badges,id',
            ];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est requis',
            'nom.string' => 'Le nom doit être une chaîne de caractères',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères',
            'prenom.required' => 'Le prénom est requis',
            'prenom.string' => 'Le prénom doit être une chaîne de caractères',
            'prenom.max' => 'Le prénom ne peut pas dépasser 255 caractères',
            'service.required' => 'Le service est requis',
            'service.string' => 'Le service doit être une chaîne de caractères',
            'service.max' => 'Le service ne peut pas dépasser 255 caractères',
            'badge_id.required' => 'L\'identifiant du badge est requis',
            'badge_id.integer' => 'L\'identifiant du badge doit être un nombre entier',
            'badge_id.exists' => 'Le badge spécifié n\'existe pas',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError('Erreur de validation', $validator->errors())->toResponse($this)
        );
    }
}
