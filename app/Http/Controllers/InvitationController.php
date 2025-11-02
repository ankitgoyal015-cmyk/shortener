<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Invitation;
use App\Models\Company;
use App\Models\Role;

class InvitationController extends Controller
{
    public function invitation()
    {
        $user = Auth::user();
        if ($user->role->name === 'SuperAdmin') {
            $companyId = base64_decode($_GET['companyId']);
            $company = Company::where('id', $companyId)->first();
            $role = Role::where('name', 'Admin')->get();
        } elseif ($user->role->name === 'Admin') {
            $company = Company::where('id', $user = $user->company_id)->first();
            $role = Role::where('name', '!=', 'SuperAdmin')->get();
        }
        $user = Auth::user()->load('role', 'company');
        return view('invitations', ['user' => $user, 'company'=>$company, 'role'=>$role]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role->name === 'SuperAdmin') {
            $companyId = base64_decode($request->query('companyId'));
            $invitations = Invitation::with(['user.role', 'inviter'])
                ->whereHas('user', fn($q) => $q->where('company_id', $companyId))
                ->orderByDesc('id')->get();
        } elseif ($user->role->name === 'Admin') {
            $invitations = Invitation::with(['user.role', 'inviter'])
                ->whereHas('user', fn($q) => 
                    $q->where('company_id', $user->company_id)
                    ->where('invited_by', $user->id)
                )
                ->orderByDesc('id')->get();
        } else {
            return response()->json(['message' => 'Access denied.'], 403);
        }

        $formatted = $invitations->map(function ($invite) {
            return [
                'name'       => $invite->user->name ?? null,
                'email'      => $invite->user->email ?? null,
                'role'       => $invite->user->role->name ?? null,
                'status'     => $invite->status,
                'invited_by' => $invite->inviter->name ?? null,
            ];
        });

        return response()->json($formatted);
    }


    public function store(Request $request)
    {
        $authUser = Auth::user();

       $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'role_id'    => 'required|integer|exists:roles,id',
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        // 1️⃣ Create user
        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make('password123'),
            'role_id'    => $request->role_id,
            'company_id' => $request->company_id,
        ]);

        // 2️⃣ Create invitation
        Invitation::create([
            'user_id'    => $user->id,
            'invited_by' => $authUser->id,
            'status'     => 'pending',
        ]);

        return response()->json([
            'message' => 'Invitation created successfully. Default password: password123',
            'user' => $user,
        ], 201);
    }
}