<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'alamat',
        'nomor_tlp',
        'member',
        'nomor_member',
        'tanggal_bergabung',
        'point_reward',
    ];

    // Aturan validasi
    public static $rules = [
        'nama' => 'required|regex:/^[a-zA-Z\s]+$/',
        'alamat' => 'required|regex:/^[0-9]+$/',
    ];
}
