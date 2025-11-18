<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Libraries\ApiResponse;

class HoraireRequest extends FormRequest
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
            'entree_matin' => 'required|date',
            'sortie_midi' => 'required|date|after:entree_matin',
            'entree_midi' => 'required|date|after:sortie_midi',
            'sortie_soir' => 'required|date|after:entree_midi',
            'utilisateur_id' => 'required|integer|exists:utilisateurs,id',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = [
                'entree_matin' => 'sometimes|date',
                'sortie_midi' => 'sometimes|date|after:entree_matin',
                'entree_midi' => 'sometimes|date|after:sortie_midi',
                'sortie_soir' => 'sometimes|date|after:entree_midi',
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
            'entree_matin.required' => 'L\'heure d\'entrée du matin est requise',
            'entree_matin.date' => 'L\'heure d\'entrée du matin doit être une date valide',
            'sortie_midi.required' => 'L\'heure de sortie du midi est requise',
            'sortie_midi.date' => 'L\'heure de sortie du midi doit être une date valide',
            'sortie_midi.after' => 'L\'heure de sortie du midi doit être après l\'entrée du matin',
            'entree_midi.required' => 'L\'heure d\'entrée du midi est requise',
            'entree_midi.date' => 'L\'heure d\'entrée du midi doit être une date valide',
            'entree_midi.after' => 'L\'heure d\'entrée du midi doit être après la sortie du midi',
            'sortie_soir.required' => 'L\'heure de sortie du soir est requise',
            'sortie_soir.date' => 'L\'heure de sortie du soir doit être une date valide',
            'sortie_soir.after' => 'L\'heure de sortie du soir doit être après l\'entrée du midi',
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
