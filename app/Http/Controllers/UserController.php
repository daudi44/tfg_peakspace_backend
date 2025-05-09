<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    // update profile
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = $request->user();
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return response()->json(['message' => 'Profile updated successfully'], 200);
    }
    // me
    // set balance
    // get balance
    // delete account
    public function deleteAccount(Request $request)
    {
        $request->user()->delete();

        return response()->json(['message' => 'Account deleted successfully'], 200);
    }
}
