<?php

namespace App\Http\Controllers;

use App\Models\attendance;
use App\Models\qrcode;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $users = attendance::when($request->search, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        })->with('user')->paginate(10);

        $users->getCollection()->transform(function ($user) {
            $user->hashed_id = Hashids::encode($user->id);
            return $user;
        });

        return inertia('Modules/Attendance/Index', [
            'users' => $users,
        ]);
    }

    public function dashboard()
    {
        $currentHour = now()->hour;
        $currentDate = now()->toDateString();
        $type = $currentHour < 12 ? 'am' : 'pm';

        $qrCode = qrcode::where('type', $type)
            ->whereDate('created_at', $currentDate)
            ->latest()
            ->first();
        $qrcodes = collect([$qrCode])->filter();

        return inertia('Dashboard', [
            'qrcode' => $qrcodes,
        ]);
    }

    public function scanner()
    {
        return inertia('Modules/Attendance/Scanner');
    }
}
