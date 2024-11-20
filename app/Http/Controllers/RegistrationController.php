<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Registration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
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

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_status' => 'required|int',
            'paid_value' => 'required|numeric',
        ]);

        $registration = Registration::findOrFail($id);
        $registration->update($validated);

        return redirect()->back()->with('success', 'Inscrição atualizada com sucesso!');
    }

    public function destroy(Registration $registration)
    {
        $registration->forceDelete();

        return redirect()->route('students.list', [
            'id' => $registration->course_id
        ])->with('success', 'Inscrição excluída com sucesso!');
    }

    public function getRegistrations(Course $course)
    {
        $students = User::all()->where('type', 1);

        return inertia('Courses/components/Registration', [
            'course' => $course,
            'students' => $students,
        ]);
    }

    public function storeRegistration(Request $request, Course $course)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'cpf' => 'required|string|max:14',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $student = User::find($validated['student_id']);

        if (!$student) {
            return redirect()->back()->withErrors(['student_id' => 'Aluno não encontrado.']);
        }

        if (Registration::where('user_id', $student->id)->where('course_id', $course->id)->exists()) {
            return redirect()->back()->withErrors(['student_id' => 'Aluno já inscrito no curso.']);
        }

        $course->students()->attach($student->id, [
            'created_at' => now(),
        ]);

        $student->update(['password' => bcrypt($validated['password'])]);

        $course->update(['remaining_slots' => $course->remaining_slots - 1]);

        return redirect()->route('courses.list')->with('success', 'Aluno inscrito com sucesso!');
    }
}
