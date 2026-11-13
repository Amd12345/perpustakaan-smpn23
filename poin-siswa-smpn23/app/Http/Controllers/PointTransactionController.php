<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\PointTransaction;
use Illuminate\Http\Request;

class PointTransactionController extends Controller
{
    public function index(Request $request)
    {
        $year = AcademicYear::where('is_active', true)->first();

        $transactions = PointTransaction::query()
            ->with(['student.class', 'source'])
            ->when($request->filled('academic_year_id'), fn ($query) => $query->where('academic_year_id', $request->input('academic_year_id')))
            ->when(! $request->filled('academic_year_id') && $year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereHas('student', fn ($q) => $q->where('name', 'like', '%'.$request->input('search').'%')
                    ->orWhere('nisn', 'like', '%'.$request->input('search').'%'));
            })
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->input('type')))
            ->when($request->filled('balance_type'), fn ($query) => $query->where('balance_type', $request->input('balance_type')))
            ->orderByDesc('transaction_date')
            ->paginate(25)
            ->withQueryString();

        return view('point_transactions.index', [
            'transactions' => $transactions,
            'year' => $year,
            'years' => AcademicYear::orderByDesc('start_year')->get(),
        ]);
    }
}
