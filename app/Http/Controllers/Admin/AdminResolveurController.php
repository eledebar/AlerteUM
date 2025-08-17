<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AdminResolveurController extends Controller
{
    public function index()
    {
        $resolveurs = User::where('role', 'resolveur')->orderBy('name')->paginate(15);
        return view('admin.resolveurs.index', compact('resolveurs'));
    }

    public function create()
    {
        return view('admin.resolveurs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','confirmed','min:8'],
        ]);

        $u = new User();
        $u->name = $data['name'];
        $u->email = $data['email'];
        $u->password = Hash::make($data['password']);
        $u->role = 'resolveur';
        $u->save();

        return redirect()->route('admin.resolveurs.edit', $u)->with('success', 'Résolveur créé.');
    }

    public function edit(User $user)
    {
        abort_unless($user->role === 'resolveur', 404);
        return view('admin.resolveurs.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->role === 'resolveur', 404);

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'password' => ['nullable','confirmed','min:8'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) $user->password = Hash::make($data['password']);
        $user->save();

        return redirect()->route('admin.resolveurs.edit', $user)->with('success', 'Résolveur mis à jour.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->role === 'resolveur', 404);
        $user->delete();
        return redirect()->route('admin.resolveurs.index')->with('success', 'Résolveur supprimé.');
    }
}
