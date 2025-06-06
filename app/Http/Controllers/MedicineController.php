<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicineRequest;
use App\Http\Requests\UpdateMedicineRequest;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->can('viewAny', Medicine::class)) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'page' => 'integer|min:1',
            'perPage' => 'integer|min:1|max:100', // Add max limit
            'sort' => 'nullable|string|max:50',
            'direction' => 'nullable|in:asc,desc',
            'filter' => 'nullable|string|max:100',
            'filterBy' => 'nullable|string|max:50',
        ]);

        $query = Medicine::query()
            ->leftJoin('medicine_stock_summaries', 'medicines.id', '=', 'medicine_stock_summaries.medicine_id')
            ->select('medicines.*', DB::raw('COALESCE(medicine_stock_summaries.total_quantity_in_stock, 0) as quantity'));

        // --- Filtering ---
        $filterValue = $request->input('filter');
        $filterColumn = $request->input('filterBy', 'name'); // Default filter column

        // Basic global filter (adjust as needed for complexity)
        // Ensure the filter column exists to prevent errors
        if ($filterValue && $filterColumn && Schema::hasColumn('medicines', $filterColumn)) {
            // Use 'where' for exact match or 'like' for partial match
            $query->where($filterColumn, 'like', '%' . $filterValue . '%');
        }

        // --- Sorting ---
        $allowedSortColumns = array_merge(
            Schema::getColumnListing('medicines'),
            ['quantity']
        );

        $sortColumn = $request->input('sort', 'name'); // Default sort column
        $sortDirection = $request->input('direction', 'desc'); // Default direction

        // Ensure the sort column exists
        if ($sortColumn && in_array($sortColumn, $allowedSortColumns)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            // Fallback sorting if provided column is invalid
            $query->orderBy('name', 'desc');
        }

        // --- Pagination ---
        $perPage = $request->input('perPage', 10); // Default page size

        // Use paginate() which includes total counts needed for React Table
        $medicines = $query->paginate($perPage)
            // Important: Append the query string parameters to pagination links
            ->withQueryString();

        return Inertia::render('Medicines/Index', [
            'medicines' => $medicines,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMedicineRequest $request)
    {

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->can('create', Medicine::class)) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validated();

        // Create the medicine
        $medicine = Medicine::create($validatedData);

        return redirect()->back()->with('success', 'Medicine created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->can('view', Medicine::class)) {
            abort(403, 'Unauthorized action.');
        }

        $medicine = Medicine::findOrFail($id);

        return Inertia::render('Medicines/Show', [
            'medicine' => $medicine,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMedicineRequest $request, string $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->can('update', Medicine::class)) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validated();

        // Find the medicine and update it
        $medicine = Medicine::findOrFail($id);
        $medicine->update($validatedData);

        return redirect()->back()->with('success', 'Medicine updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->can('delete', Medicine::class)) {
            abort(403, 'Unauthorized action.');
        }

        // Find the medicine and delete it
        $medicine = Medicine::findOrFail($id);
        $medicine->delete();

        // For Inertia, you can either redirect or return a JSON response
        if (request()->wantsJson()) {
            return response()->json(['message' => 'Medicine deleted successfully']);
        }

        return redirect()->route('medicines.index')
            ->with('success', 'Medicine deleted successfully.');
    }
}
