<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCourses = Course::count();
        $totalStudents = User::where('type', 1)->count();
        $totalAdmins = User::where('type', 0)->count();
        $pendingPurchases = Registration::where('payment_status', 0)->count();
        $approvedPurchases = Registration::where('payment_status', 1)->count();
        $canceledPurchases = Registration::where('payment_status', 2)->count();

        return Inertia::render('Dashboard', [
            'totalCourses' => $totalCourses,
            'totalStudents' => $totalStudents,
            'totalAdmins' => $totalAdmins,
            'approvedPurchases' => $approvedPurchases,
            'pendingPurchases' => $pendingPurchases,
            'canceledPurchases' => $canceledPurchases,
        ]);
    }
}
