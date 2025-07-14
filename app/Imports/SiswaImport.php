<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithValidation;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
     use SkipsFailures;

    public function model(array $row)
    {
        $validator = Validator::make($row, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:6',
            'nis' => 'required',
            'kelas_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return null;
        }

        $user = User::updateOrCreate(
            ['email' => $row['email']],
            [
                'name' => $row['name'],
                'password' => !empty($row['password'])
                    ? Hash::make($row['password'])
                    : User::where('email', $row['email'])->value('password'),
                'role' => 'siswa'
            ]
        );

        Siswa::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nis' => $row['nis'],
                'kelas_id' => $row['kelas_id']
            ]
        );

        return $user;
    }

    public function rules(): array
    {
        return [
            '*.name' => 'required|string|max:255',
            '*.email' => 'required|email|max:255',
            '*.password' => 'nullable|string|min:6',
            '*.nis' => 'required',
            '*.kelas_id' => 'required|integer',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.name.required' => 'Nama wajib diisi.',
            '*.email.required' => 'Email wajib diisi.',
            '*.email.email' => 'Format email tidak valid.',
            '*.nis.required' => 'NIS wajib diisi.',
            '*.kelas_id.required' => 'Kelas wajib diisi.',
        ];
    }
}