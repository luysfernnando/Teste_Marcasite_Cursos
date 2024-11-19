<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Registration;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegistrationController extends Controller
{
    public function showCourses()
    {
        $courses = Course::all();
        return Inertia::render('Courses/Index', [
            'courses' => $courses,
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students',
            'phone' => 'required|string|max:15',
            'course_id' => 'required|exists:courses,id',
        ]);

        $student = Student::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'phone' => $validated['phone'],
            ]
        );

        Registration::create([
            'student_id' => $student->id,
            'course_id' => $validated['course_id'],
            'payment_status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Inscrição realizada com sucesso!');
    }
}
