<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class ScanQrRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nis' => ['required', 'string', 'max:50'],
            'sesi_id' => ['required', 'integer', 'min:1'],
            'recorded_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * Custom validation messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nis.required' => 'Nomor Induk Santri (NIS) wajib diisi.',
            'nis.string' => 'Format NIS harus berupa teks.',
            'nis.max' => 'NIS maksimal 50 karakter.',
            'sesi_id.required' => 'ID Sesi Absensi wajib diisi.',
            'sesi_id.integer' => 'ID Sesi Absensi harus berupa bilangan bulat.',
            'sesi_id.min' => 'ID Sesi Absensi tidak valid.',
            'recorded_by.exists' => 'User pencatat tidak terdaftar dalam sistem.',
        ];
    }

    /**
     * Handle a failed validation attempt with JSON structured response.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal: parameter NIS atau Sesi ID tidak valid.',
            'errors' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
