<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Book;

class Loan extends Model
{
    protected $fillable = [
        'book_id',
        'nama_peminjam',
        'kelas',
        'tanggal_pinjam',
        'batas_pengembalian',
        'tanggal_kembali',
        'qty',
        'is_returned',
        'denda',
        'denda_hari'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}