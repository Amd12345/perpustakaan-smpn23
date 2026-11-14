<?php
namespace App\Exports;

use App\Models\Loan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LoansExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $from;
    protected $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to   = $to;
    }

    public function query()
    {
        return Loan::with('book')
            ->whereBetween('tanggal_pinjam', [$this->from, $this->to])
            ->orderBy('tanggal_pinjam', 'asc');
    }

    public function headings(): array
    {
        return [
            'Nama Peminjam',
            'Judul Buku',
            'Qty',
            'Tanggal Pinjam',
            'Tanggal Kembali',
            'Status',
            'Denda (Rp)',
            'Terlambat (Hari)',
        ];
    }

    public function map($loan): array
    {
        return [
            $loan->nama_peminjam,
            $loan->book->judul ?? '-',
            $loan->qty,
            \Carbon\Carbon::parse($loan->tanggal_pinjam)->format('d-m-Y'),
            $loan->tanggal_kembali
                ? \Carbon\Carbon::parse($loan->tanggal_kembali)->format('d-m-Y')
                : 'Belum kembali',
            $loan->is_returned ? 'Dikembalikan' : 'Dipinjam',
            $loan->denda ?? 0,
            $loan->denda_hari ?? 0,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // heading bold
        ];
    }
}