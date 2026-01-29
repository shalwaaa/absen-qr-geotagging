<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Classroom;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Lewati jika baris kosong
        if (!isset($row['nama']) || !isset($row['nip_nis'])) {
            return null;
        }

        // mencari ID Kelas berdasarkan Nama Kelas di Excel 
        $classroom_id = null;
        if (isset($row['kelas'])) {
            $classroom = Classroom::where('name', $row['kelas'])->first();
            if ($classroom) {
                $classroom_id = $classroom->id;
            }
        }

        return new User([
            'name'     => $row['nama'],
            'email'    => null, 
            'nip_nis'  => $row['nip_nis'],
            'role'     => $row['role'],
            'classroom_id' => $classroom_id,
            'password' => Hash::make($row['nip_nis']),
        ]);
    }
}