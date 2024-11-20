<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        return Inertia::render('Courses/List', [
            'courses' => $courses,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'remaining_slots' => 'required|integer|min:0',
        ]);

        Course::create($validated);

        return redirect()->route('courses.list')->with('success', 'Curso criado com sucesso!');
    }

    public function edit(Course $course)
    {
        return Inertia::render('Courses/Edit', [
            'course' => $course,
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'remaining_slots' => 'required|integer|min:0',
        ]);

        $validated['is_active'] = (int) $validated['is_active'];

        $course->update($validated);

        return redirect()->route('courses.list')->with('success', 'Curso atualizado com sucesso!');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('courses.list')->with('success', 'Curso deletado com sucesso!');
    }

    public function showStudents($id)
    {
        $course = Course::findOrFail($id);

        $students = $course->students()->get();

        return inertia('Courses/components/Students', [
            'course' => $course,
            'students' => $students,
        ]);
    }

}
