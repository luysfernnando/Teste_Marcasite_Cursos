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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('course_logo', 'public');
            $validated['image_path'] = $path;
        }

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

    public function uploadPhoto(Request $request, $id)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $course = Course::findOrFail($id);

        if (!$course) {
            return redirect()->back()->withErrors(['user' => 'Usuário não encontrado']);
        }

        if ($request->file('photo')) {
            $path = $request->file('photo')->store('course_logo', 'public');

            $course->image_path = $path;
            $course->save();
        }

        return redirect()->route('courses.edit', $id)->with('success', 'Imagem do curso atualizada com sucesso!');
    }
}
