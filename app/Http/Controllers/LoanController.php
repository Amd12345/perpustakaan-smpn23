<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LoansExport;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $loans = Loan::with('book')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('loans.index', compact('loans'));
    }

    public function create()
    {
        $books = Book::all();

        return view('loans.create', compact('books'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id'        => 'required|exists:books,id',
            'nama_peminjam'  => 'required|string|max:255',
            'kelas'          => 'required',
            'tanggal_pinjam' => 'required|date',
            'qty'            => 'required|integer|min:1',
        ]);

        $book = Book::findOrFail($request->book_id);

        if ($book->stok < 1) {
            return back()->with('error', 'Buku ini sudah habis stoknya!');
        }

        if ($request->qty > $book->stok) {
            return back()
                ->withInput()
                ->with('error', "Stok tidak cukup! Stok tersedia: {$book->stok}");
        }

        DB::transaction(function () use ($request) {

            $batasPengembalian = Carbon::parse($request->tanggal_pinjam)
                ->addDays(7);

            Loan::create([
                'book_id'             => $request->book_id,
                'nama_peminjam'       => $request->nama_peminjam,
                'kelas'               => $request->kelas,
                'tanggal_pinjam'      => $request->tanggal_pinjam,
                'batas_pengembalian'  => $batasPengembalian,
                'qty'                 => $request->qty,
                'is_returned'         => false,
                'tanggal_kembali'     => null,
            ]);

            Book::lockForUpdate()
                ->findOrFail($request->book_id)
                ->decrement('stok', $request->qty);
        });

        return redirect('/loans')->with('success', 'Peminjaman berhasil!');
    }

    public function returnBook($id)
    {
        $loan = Loan::with('book')->findOrFail($id);

        if ($loan->is_returned) {
            return back()->with('error', 'Buku ini sudah dikembalikan sebelumnya!');
        }

        DB::transaction(function () use ($loan) {
            $now = Carbon::today();

            $tanggalPinjam = Carbon::parse($loan->tanggal_pinjam)->startOfDay();

            $lamaPinjam = $tanggalPinjam->diffInDays($now);

            $telatHari = 0;
            $denda = 0;

            if ($lamaPinjam > 7) {
                $telatHari = $lamaPinjam - 7;
                $denda = $telatHari * 5000;
            }

            $loan->update([
                'tanggal_kembali' => $now,
                'is_returned'     => true,
                'denda_hari'      => $telatHari,
                'denda'           => $denda,
            ]);

            $loan->book->increment('stok', $loan->qty);
        });

        return back()->with('success', 'Buku berhasil dikembalikan!');
    }

    public function export(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        $from     = Carbon::parse($request->from)->startOfDay();
        $to       = Carbon::parse($request->to)->endOfDay();
        $filename = 'peminjaman_' . $from->format('d-m-Y') . '_sd_' . $to->format('d-m-Y') . '.csv';

        return Excel::download(new LoansExport($from, $to), $filename);
    }
}
