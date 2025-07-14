<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
      public function model(array $row)
    {
        $user = User::updateOrCreate(
            ['email' => $row['email']],
            [
                'name' => $row['name'],
                'password' => Hash::make($row['password']),
                'role' => $row['role']
            ]
        );

        if ($user->role === 'siswa') {
            Siswa::updateOrCreate(
                ['user_id' => $user->id],
                ['kelas_id' => $row['kelas_id'], 'nis' => $row['nis']]
            );
        } elseif ($user->role === 'guru') {
            Guru::updateOrCreate(['user_id' => $user->id], ['nip' => $row['nip']]);
        }

        return $user;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'role' => 'required',
            'kelas_id' => 'nullable',
            'nis' => 'nullable',
            'nip' => 'nullable'
        ];
    }
}
