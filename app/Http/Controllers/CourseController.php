<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Http\Requests\Course\UploadCoursePhotoRequest;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

    public function listUserCourses()
    {
        $user = auth()->user();
        $registrations = Registration::where('user_id', $user->id)->get();

        return Inertia::render('UserCourses/List', [
            'registrations' => $registrations->map(function ($registrations) {
                return [
                    'id' => $registrations->id,
                    'course_id' => $registrations->course->id,
                    'name' => $registrations->course->name,
                    'enrolled_at' => $registrations->enrolled_at,
                    'payment_status' => $registrations->payment_status,
                    'paid_value' => $registrations->paid_value,
                ];
            }),
        ]);
    }

    public function editUserCourse(Course $course)
    {
        $user = auth()->user();

        $registrations = Registration::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        return Inertia::render('UserCourses/Edit', [
            'course' => [
                'id' => $course->id,
                'name' => $course->name,
                'enrolled_at' => $registrations->enrolled_at,
                'payment_status' => $registrations->payment_status,
                'paid_value' => $registrations->paid_value,
                'description' => $course->description,
                'image_path' => $course->image_path,
            ],
        ]);
    }

    public function showcase()
    {
        $user = auth()->user();
        $courses = Course::where('is_active', 1)->get();

        return Inertia::render('Showcase/List', [
            'courses' => $courses,
            'registered_courses' => $user->courses,
        ]);
    }
}
