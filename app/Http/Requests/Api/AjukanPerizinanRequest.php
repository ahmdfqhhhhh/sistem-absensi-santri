<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class AjukanPerizinanRequest extends FormRequest
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
            'santri_id' => ['required', 'integer', 'exists:santris,id'],
            'jenis_izin' => ['required', 'string', 'in:pulang,keluar_komplek,sakit,sambangan_keluarga,tugas_pondok'],
            'keperluan' => ['required', 'string', 'min:5', 'max:1000'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'penjemput_nama' => ['nullable', 'string', 'max:100'],
            'penjemput_hubungan' => ['nullable', 'string', 'max:50'],
            'bukti_dokumen_path' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'santri_id.required' => 'ID Santri wajib diisi.',
            'santri_id.exists' => 'Santri tidak ditemukan dalam sistem.',
            'jenis_izin.required' => 'Jenis izin wajib dipilih.',
            'jenis_izin.in' => 'Jenis izin harus salah satu dari: pulang, keluar_komplek, sakit, sambangan_keluarga, tugas_pondok.',
            'keperluan.required' => 'Keperluan izin wajib dijelaskan.',
            'tanggal_mulai.required' => 'Waktu mulai perizinan wajib diisi.',
            'tanggal_selesai.required' => 'Waktu selesai perizinan wajib diisi.',
            'tanggal_selesai.after' => 'Waktu selesai perizinan harus setelah waktu mulai.',
        ];
    }

    /**
     * Handle failed validation.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi pengajuan perizinan gagal.',
            'errors' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
