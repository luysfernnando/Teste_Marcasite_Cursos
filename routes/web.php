<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Cursos
Route::get('/courses', [CourseController::class, 'index'])->name('courses.list');
Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
Route::get('/courses/{courseId}/students', [CourseController::class, 'showStudents'])->name('courses.students');
Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.delete');

// Usuários
Route::get('/users', [UserController::class, 'index'])->name('users.index');

// Configurações
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

// Registro de cursos
Route::post('/register', [RegistrationController::class, 'register'])->name('courses.register');

require __DIR__.'/auth.php';
