<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Libraries\ApiResponse;

class BadgeRequest extends FormRequest
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
        $badgeId = $this->route('badge');

        $rules = [
            'numero' => 'required|integer|unique:badges,numero',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = [
                'numero' => 'sometimes|integer|unique:badges,numero,' . $badgeId,
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
            'numero.required' => 'Le numéro du badge est requis',
            'numero.integer' => 'Le numéro du badge doit être un nombre entier',
            'numero.unique' => 'Ce numéro de badge existe déjà',
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
