<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AdminResolveurController extends Controller
{
    public function index()
    {
        $resolveurs = User::whereIn('name', array_map(fn($n)=>"Soporte".str_pad($n,2,'0',STR_PAD_LEFT), range(1,10)))
            ->orWhere('role','resolveur')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.resolveurs.index', compact('resolveurs'));
    }

    public function create()
    {
        return view('admin.resolveurs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required','string','max:100'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required','min:6'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'resolveur',
        ]);

        return redirect()->route('admin.resolveurs.index')->with('success','Résolveur créé');
    }

    public function edit(User $user)
    {
        return view('admin.resolveurs.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => ['required','string','max:100'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'password' => ['nullable','min:6'],
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->role = 'resolveur';
        $user->save();

        return redirect()->route('admin.resolveurs.index')->with('success','Résolveur mis à jour');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success','Résolveur supprimé');
    }
}
