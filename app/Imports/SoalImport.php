<?php

namespace App\Imports;

use App\Models\Soal;
use App\Models\Ujian;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SoalImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param Collection $collection
    */
   public function model(array $row)
    {
        $ujian = Ujian::where('id', $row['ujian'])->first();
        if (!$ujian) {
            return null;
        }
        return new Soal([
            'ujian_id' => $ujian->id,
            'pertanyaan' => $row['pertanyaan'],
            'jawaban_benar' => $row['jawaban_benar'],
        ]);
    }

    public function rules(): array
    {
        return [
            'ujian' => 'required',
            'pertanyaan' => 'required',
            'jawaban_benar' => 'nullable',
        ];
    }
}
