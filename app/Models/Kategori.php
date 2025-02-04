<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'nama_kategori',
    ];
    
    // Aturan validasi
    public static $rules = [
        'nama_kategori' => 'required|regex:/^[a-zA-Z\s]+$/',
    ];
}
