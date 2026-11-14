<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Loan;

class Book extends Model
{
    // Kolom yang boleh di-isi menggunakan create() atau update()
    protected $fillable = [
        'judul',
        'penerbit',
        'tahun_terbit',
        'stok',
    ];


    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}