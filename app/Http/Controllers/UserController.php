<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return Inertia::render('Users/List', [
            'users' => $users,
        ]);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return inertia('Users/Edit', ['user' => $user]);
    }

    public function create()
    {
        return inertia('Users/Edit', ['user' => null]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required',
            'is_active' => 'required',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|max:14|unique:users,cpf',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'is_active' => $validated['is_active'],
            'email' => $validated['email'],
            'cpf' => $validated['cpf'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required',
            'is_active' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'cpf' => 'required|max:14|unique:users,cpf,' . $id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'is_active' => $validated['is_active'],
            'email' => $validated['email'],
            'cpf' => $validated['cpf'],
            'password' => $validated['password'] ? bcrypt($validated['password']) : $user->password,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuário excluído com sucesso!');
    }

    public function uploadPhoto(Request $request, $id)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::find($id);

        if (!$user) {
            return redirect()->back()->withErrors(['user' => 'Usuário não encontrado']);
        }

        // Salvar a foto no sistema de arquivos
        if ($request->file('photo')) {
            $path = $request->file('photo')->store('profile_photos', 'public');

            // Atualizar o campo profile_photo_path no usuário
            $user->profile_photo_path = $path;
            $user->save();
        }

        return back()->with('success', 'Foto de perfil atualizada com sucesso!');
    }
}
