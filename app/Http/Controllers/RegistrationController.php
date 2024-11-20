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

    public function edit(Registration $registration)
    {
        return Inertia::render('Students/Edit', [
            'registration' => $registration,
        ]);
    }

    public function update(Request $request, Registration $registration)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'cpf' => 'required|string|max:14',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $registration->update($validated);

        return redirect()->route('students.list', $registration->course_id)->with('success', 'Inscrição atualizada com sucesso!');
    }

    public function destroy(Registration $registration)
    {

        dd($registration);

        $registration->forceDelete();

//        $registration->course->update(['remaining_slots' => $registration->course->remaining_slots + 1]);

        return redirect()->route('students.list', 'id' === $registration->course_id)->with('success', 'Inscrição excluída com sucesso!');
    }

    public function getRegistrations(Course $course)
    {
        // Buscando todos os alunos disponíveis
        $students = User::all()->where('type', 1);  // Adapte conforme sua lógica de alunos

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

        // Verificar se o aluno existe
        $student = User::find($validated['student_id']);

        if (!$student) {
            return redirect()->back()->withErrors(['student_id' => 'Aluno não encontrado.']);
        }

        // Verificar se o aluno já está inscrito no curso
        if (Registration::where('user_id', $student->id)->where('course_id', $course->id)->exists()) {
            return redirect()->back()->withErrors(['student_id' => 'Aluno já inscrito no curso.']);
        }

        // Registrar o aluno no curso
        $course->students()->attach($student->id, [
            'created_at' => now(),
        ]);

        // Atualizar senha do aluno
        $student->update(['password' => bcrypt($validated['password'])]);

        // Atualizar a quantidade de vagas restantes
        $course->update(['remaining_slots' => $course->remaining_slots - 1]);

        return redirect()->route('courses.list')->with('success', 'Aluno inscrito com sucesso!');
    }
}
