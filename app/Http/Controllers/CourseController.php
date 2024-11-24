<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Http\Requests\Course\UploadCoursePhotoRequest;
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

    public function store(StoreCourseRequest $request)
    {
        $validated = $request->validated();

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

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $validated = $request->validated();

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

    public function uploadPhoto(UploadCoursePhotoRequest $request, $id)
    {
        $course = Course::findOrFail($id);

        if ($request->file('photo')) {
            $path = $request->file('photo')->store('course_logo', 'public');

            $course->image_path = $path;
            $course->save();
        }

        return redirect()->route('courses.edit', $id)->with('success', 'Imagem do curso atualizada com sucesso!');
    }
}
