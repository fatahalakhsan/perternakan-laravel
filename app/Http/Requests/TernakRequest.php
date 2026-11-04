<?php

namespace App\Http\Requests;

use App\Models\Ternak;
use Illuminate\Foundation\Http\FormRequest;

/** Validasi di sisi server untuk tambah (POST) dan ubah (PUT) ternak. */
class TernakRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // hak akses sudah dijaga middleware role di routes/api.php
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('kode')) {
            $this->merge(['kode' => strtoupper(trim((string) $this->input('kode')))]);
        }
    }

    public function rules(): array
    {
        $rules = [
            'jenis'         => ['required', 'in:'.implode(',', Ternak::JENIS)],
            'ras'           => ['nullable', 'string', 'max:60'],
            'jenis_kelamin' => ['required', 'in:'.implode(',', Ternak::KELAMIN)],
            'tanggal_lahir' => ['nullable', 'date', 'before_or_equal:today'],
            'bobot_kg'      => ['required', 'numeric', 'gt:0', 'max:9999.9'],
            'status'        => ['required', 'in:'.implode(',', Ternak::STATUS)],
            'harga_beli'    => ['nullable', 'integer', 'min:0'],
            'catatan'       => ['nullable', 'string', 'max:1000'],
        ];

        // Kode hanya diisi saat membuat; tidak bisa diubah lewat PUT
        if ($this->isMethod('POST')) {
            $rules['kode'] = ['required', 'regex:/^[A-Z]{2}-\d{3}$/', 'unique:ternak,kode'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'required'        => ':attribute wajib diisi.',
            'in'              => ':attribute tidak valid.',
            'unique'          => ':attribute sudah dipakai ternak lain.',
            'numeric'         => ':attribute harus berupa angka.',
            'integer'         => ':attribute harus berupa bilangan bulat.',
            'gt'              => ':attribute harus lebih dari 0.',
            'min'             => ':attribute tidak boleh negatif.',
            'max'             => ':attribute terlalu besar atau terlalu panjang.',
            'date'            => ':attribute bukan tanggal yang valid.',
            'before_or_equal' => 'Tanggal lahir tidak boleh di masa depan.',
            'kode.regex'      => 'Format kode: dua huruf, strip, tiga angka (contoh KB-030).',
        ];
    }

    public function attributes(): array
    {
        return [
            'kode' => 'Kode', 'jenis' => 'Jenis', 'ras' => 'Ras', 'jenis_kelamin' => 'Jenis kelamin',
            'tanggal_lahir' => 'Tanggal lahir', 'bobot_kg' => 'Bobot', 'status' => 'Status',
            'harga_beli' => 'Harga beli', 'catatan' => 'Catatan',
        ];
    }
}
