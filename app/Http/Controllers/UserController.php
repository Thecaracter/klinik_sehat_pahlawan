<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'admin')->get();
        return view('pages.user', compact('users'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => ['required', 'string', Rule::in(['dokter', 'bidan'])],
            ]);

            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('user.index')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->route('user.index')->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }
    public function destroy(string $id)
    {
        try {
            $user = User::find($id);
            $user->delete();
            return redirect()->route('user.index')->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('user.index')->with('error', 'User gagal dihapus');
        }
    }
}
