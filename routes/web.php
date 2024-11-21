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
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.list');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Usuários
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/{id}/upload-photo', [UserController::class, 'uploadPhoto'])->name('users.uploadPhoto');

    // Cursos
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.list');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::get('/courses/{courseId}/students', [CourseController::class, 'showStudents'])->name('courses.students');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.delete');
    Route::get('/courses/{id}/students', [CourseController::class, 'showStudents'])->name('courses.students');
    Route::get('courses/{course}/registration/new', [RegistrationController::class, 'getRegistrations'])->name('registration.get');
    Route::post('courses/{course}/registration', [RegistrationController::class, 'storeRegistration'])->name('registration.store');

    // Inscrições
    Route::get('/registration/{id}/students', [RegistrationController::class, 'index'])->name('students.list');
    Route::get('/registration/{id}/edit', [RegistrationController::class, 'edit'])->name('students.edit');
    Route::put('/registration/{id}', [RegistrationController::class, 'update'])->name('students.update');
    Route::delete('/registration/{id}', [RegistrationController::class, 'destroy'])->name('students.destroy');

    // Configurações
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

    // Registro de cursos
    Route::post('/register', [RegistrationController::class, 'register'])->name('courses.register');
});

require __DIR__.'/auth.php';
