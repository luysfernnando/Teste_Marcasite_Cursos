<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UploadUserPhotoRequest;
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

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

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

    public function update(UpdateUserRequest $request, $id)
    {
        $validated = $request->validated();

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

    public function uploadPhoto(UploadUserPhotoRequest $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->file('photo')) {
            $path = $request->file('photo')->store('profile_photos', 'public');

            $user->profile_photo_path = $path;
            $user->save();
        }

        return back()->with('success', 'Foto de perfil atualizada com sucesso!');
    }
}
