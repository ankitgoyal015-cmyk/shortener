<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortUrl;
use Illuminate\Support\Str;

class ShortUrlController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // SuperAdmin restriction
        if ($user->role->name === 'SuperAdmin') {
            return ShortUrl::with(['user','company'])
                ->get();
        }

        // Admin - see URLs not from their own company
        if ($user->role->name === 'Admin') {
            return ShortUrl::with(['user','company'])
                ->where('company_id','=',$user->company_id)
                ->get();
        }

        // Member - see URLs not created by themselves
        if ($user->role->name != 'SuperAdmin' && $user->role->name != 'Admin') {
            return ShortUrl::with(['user','company'])
                ->where('user_id','=',$user->id)
                ->get();
        }

        // Sales / Manager - can see their own URLs too
        return ShortUrl::with(['user','company'])->get();
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (in_array($user->role->name, ['SuperAdmin'])) {
            return response()->json(['message' => 'Not allowed to create short URLs'], 403);
        }

        $request->validate(['original_url' => 'required|url']);
        $code = Str::random(8);
        while (ShortUrl::where('short_code', $code)->exists()) {
            $code = Str::random(8);
        }
        $short = ShortUrl::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'original_url' => $request->original_url,
            'short_code' => $code,
        ]);
        return response()->json($short);
    }

    public function redirect($code)
    {
        $shortUrl = ShortUrl::where('short_code', $code)
            ->firstOrFail();

        return redirect()->away($shortUrl->original_url);
    }
}
