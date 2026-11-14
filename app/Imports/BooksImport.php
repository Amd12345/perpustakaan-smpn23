<?php
namespace App\Imports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BooksImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $book = Book::where('judul', $row['judul'])->first();

        if ($book) {
            // Buku sudah ada → tambah stok saja, tidak buat data baru
            $book->increment('stok', (int) $row['stok']);
            return null;
        }

        // Buku belum ada → buat data baru
        return new Book([
            'judul'        => $row['judul'],
            'penerbit'     => $row['penerbit'],
            'tahun_terbit' => $row['tahun_terbit'],
            'stok'         => (int) $row['stok'],
        ]);
    }

    public function rules(): array
    {
        return [
            'judul'        => 'required|string',
            'penerbit'     => 'required|string',
            'tahun_terbit' => 'required',
            'stok'         => 'required|integer|min:1',
        ];
    }
}