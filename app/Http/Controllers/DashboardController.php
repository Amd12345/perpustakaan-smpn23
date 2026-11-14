<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;

class DashboardController extends Controller
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

        $dipinjam = Loan::where('is_returned', false)->count();
        $tersedia = Book::sum('stok');

        return view('dashboard', compact('books', 'dipinjam', 'tersedia'));
    }
}
