<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JenisPelatihanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'nama_jenis' => trim($this->nama_jenis ?? ''),
            'deskripsi' => trim($this->deskripsi ?? ''),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'nama_jenis' => 'required|string|min:3|max:255|not_regex:/^\s*$/',
                'deskripsi' => 'nullable|string|max:1000',
            ];
        } else {
            return [
                'nama_jenis' => 'required|string|min:3|max:255|not_regex:/^\s*$/',
                'deskripsi' => 'nullable|string|max:1000',
            ];
        }
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'id_jenis.required' => 'ID Jenis harus diisi',
            'id_jenis.integer' => 'ID Jenis harus berupa angka',
            'id_jenis.min' => 'ID Jenis minimal 1',
            'id_jenis.max' => 'ID Jenis maksimal 999',
            'id_jenis.unique' => 'ID Jenis sudah terdaftar, gunakan ID yang berbeda',
            'nama_jenis.required' => 'Nama Jenis harus diisi',
            'nama_jenis.string' => 'Nama Jenis harus berupa teks',
            'nama_jenis.min' => 'Nama Jenis minimal 3 karakter',
            'nama_jenis.max' => 'Nama Jenis maksimal 255 karakter',
            'nama_jenis.not_regex' => 'Nama Jenis tidak boleh hanya berisi spasi',
            'deskripsi.string' => 'Deskripsi harus berupa teks',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter',
        ];
    }
}
