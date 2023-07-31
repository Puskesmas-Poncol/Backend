<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use HasFactory;

    protected $table = 'data';

    protected $fillable = [
        'NIK',
        'nama_anak',
        'tgl_lahir',
        'umur_tahun',
        'umur_bulan',
        'jenis_kelamin',
        'nama_ortu',
        'nik_ortu',
        'hp_ortu',
        'PKM',
        'KEL',
        'POSY',
        'RT',
        'RW',
        'ALAMAT',
        'TANGGALUKUR',
        'TINGGI',
        'BERAT',
        'LILA',
        'vita',
        'lingkar_kepala',
        'asi_bulan_1',
        'asi_bulan_2',
        'asi_bulan_3',
        'asi_bulan_4',
        'asi_bulan_5',
        'asi_bulan_6',
        'pemberian_ke',
        'sumber_pmt',
        'pemberian_pusat',
        'tahun_produksi',
        'pemberian_daerah',
        'id_posyandu',
        'id_upload'
    ];
}
