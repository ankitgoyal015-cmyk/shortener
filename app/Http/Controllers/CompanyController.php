<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class CompanyController extends Controller
{
    /**
     * Show all companies (SuperAdmin only)
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role->name !== 'SuperAdmin') {
            return response()->json([
                'message' => 'Only SuperAdmin can view all companies.'
            ], 403);
        }

        $companies = Company::with('creator')->orderBy('id', 'desc')->get();

        return response()->json($companies);
    }

    /**
     * Create a new company (SuperAdmin only)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role->name !== 'SuperAdmin') {
            return response()->json([
                'message' => 'Only SuperAdmin can create companies.'
            ], 403);
        }

        // ✅ Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:companies,name',
        ]);

        try {
            $company = Company::create([
                'name' => $validated['name'],
                'created_by' => $user->id,
            ]);

            return response()->json([
                'message' => 'Company created successfully!',
                'company' => $company
            ], 201);
        } 
        catch (QueryException $e) {
            // Duplicate entry or DB issue
            if ($e->getCode() == '23000') {
                return response()->json([
                    'message' => 'Company name already exists. Please use a different name.'
                ], 422);
            }

            return response()->json([
                'message' => 'An unexpected database error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        } 
        catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred while creating the company.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}