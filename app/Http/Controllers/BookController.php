<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BooksImport;
use App\Exports\BooksExport;
use Maatwebsite\Excel\Validators\ValidationException;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $books = Book::when($search, function ($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%");
        })
        ->orderBy('judul', 'asc')
        ->paginate(10)
        ->withQueryString();

        return view('books.index', compact('books'));
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'        => 'required|string|max:255',
            'penerbit'     => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
            'stok'         => 'required|integer|min:0',
        ]);

        Book::create($validated);

        return redirect('/books')
            ->with('success', 'Buku berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $book = Book::findOrFail($id);

        return view('books.edit', compact('book'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'judul'        => 'required|string|max:255',
            'penerbit'     => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
            'stok'         => 'required|integer|min:0',
        ]);

        Book::findOrFail($id)->update($validated);

        return redirect('/books')
            ->with('success', 'Buku berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Book::destroy($id);

        return redirect('/books')
            ->with('success', 'Buku berhasil dihapus!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        try {
            Excel::import(new BooksImport, $request->file('file'));
            return back()->with('success', 'Data buku berhasil diimport!');
        } catch (ValidationException $e) {
            // Kalau ada baris di Excel yang tidak valid
            $errors = collect($e->failures())
                ->map(fn($f) => "Baris {$f->row()}: {$f->attribute()} — " . implode(', ', $f->errors()))
                ->join('<br>');
            return back()->with('error', $errors);
        }
    }

    public function export()
    {
        return Excel::download(new BooksExport, 'Data-Buku-smpn23.csv');
    }
}
