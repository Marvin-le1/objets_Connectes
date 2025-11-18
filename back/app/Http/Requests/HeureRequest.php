<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Libraries\ApiResponse;

class HeureRequest extends FormRequest
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
            'entree_sortie' => 'required|boolean',
            'heure' => 'required|date',
            'utilisateur_id' => 'required|integer|exists:utilisateurs,id',
        ];

        // Pour les mises à jour (PUT/PATCH), rendre les champs optionnels
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = [
                'entree_sortie' => 'sometimes|boolean',
                'heure' => 'sometimes|date',
                'utilisateur_id' => 'sometimes|integer|exists:utilisateurs,id',
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
            'entree_sortie.required' => 'Le champ entrée/sortie est requis',
            'entree_sortie.boolean' => 'Le champ entrée/sortie doit être un booléen',
            'heure.required' => 'L\'heure est requise',
            'heure.date' => 'L\'heure doit être une date valide',
            'utilisateur_id.required' => 'L\'identifiant de l\'utilisateur est requis',
            'utilisateur_id.integer' => 'L\'identifiant de l\'utilisateur doit être un nombre entier',
            'utilisateur_id.exists' => 'L\'utilisateur spécifié n\'existe pas',
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
