<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BooksExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Book::all([
            'judul',
            'penerbit',
            'tahun_terbit',
            'stok',
        ]);
    }

    public function headings(): array
    {
        return [
            'Judul',
            'Penerbit',
            'Tahun Terbit',
            'Stok',
        ];
    }
}