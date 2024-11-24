<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Registration;
use App\Models\User;
use App\Http\Requests\Registration\StoreRegistrationRequest;
use App\Http\Requests\Registration\UpdateRegistrationRequest;
use Inertia\Inertia;

class RegistrationController extends Controller
{
    public function index($id)
    {
        $course = Course::findOrFail($id);

        $students = $course->students()->get();

        return inertia('Students/List', [
            'course' => $course,
            'students' => $students,
        ]);
    }

    public function edit($id)
    {
        $registration = Registration::findOrFail($id);
        $course = Course::findOrFail($registration->course_id);
        $student = User::findOrFail($registration->user_id);

        return Inertia::render('Students/Edit', [
            'registration' => $registration,
            'course' => $course,
            'student' => $student,
        ]);
    }

    public function update(UpdateRegistrationRequest $request, $id)
    {
        $validated = $request->validated();

        $registration = Registration::findOrFail($id);
        $registration->update($validated);

        return redirect()->back()->with('success', 'Inscrição atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $registration = Registration::findOrFail($id);
        $registration->delete();

        $registration->course->update(['remaining_slots' => $registration->course->remaining_slots + 1]);

        return redirect()->back()->with('success', 'Inscrição excluída com sucesso!');
    }

    public function getRegistrations(Course $course)
    {
        $registration = Registration::all()->where('course_id', $course->id);
        $students = User::all()->where('type', 1);

        return Inertia::render('Courses/components/Registration', [
            'registration' => $registration,
            'course' => $course,
            'students' => $students,
        ]);
    }

    public function storeRegistration(StoreRegistrationRequest $request, Course $course)
    {
        $validated = $request->validated();

        $student = User::find($validated['student_id']);

        if (!$student) {
            return redirect()->back()->withErrors(['error' => 'Aluno não encontrado.']);
        }

        if (Registration::where('user_id', $student->id)->where('course_id', $course->id)->exists()) {
            return redirect()->back()->withErrors(['error' => 'Aluno já inscrito no curso.']);
        }

        $course->students()->attach($student->id, [
            'created_at' => now(),
        ]);

        $course->update(['remaining_slots' => $course->remaining_slots - 1]);
        $student->update(['cpf' => $validated['cpf']]);

        return redirect()->route('courses.list')->with('success', 'Aluno inscrito com sucesso!');
    }
}
