<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $profile = $user->customerProfile;
        
        return view('client::profile.index', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
        ]);

        $user->update([
            'full_name' => $validated['full_name'] ?? $user->full_name,
            'email' => $validated['email'] ?? $user->email,
        ]);

        if ($user->customerProfile) {
            $user->customerProfile->update([
                'birth_date' => $validated['birth_date'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
            ]);
        }

        return back()->with('success', 'Профиль обновлен');
    }
}
