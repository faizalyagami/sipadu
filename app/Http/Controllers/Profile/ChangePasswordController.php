<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    /**
     * Show change password form
     */
    public function showChangeForm()
    {
        return view('auth.change-password');
    }

    /**
     * Change password
     */
    public function change(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = auth()->user();
        
        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }
        
        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();
        
        return back()->with('success', 'Password berhasil diubah.');
    }
}