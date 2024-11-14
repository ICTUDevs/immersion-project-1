<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\qrcode;
use App\Models\attendance;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Validator;

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
        $date = Carbon::today()->toDateString();

        // Fetch users who have a time-in record for today with status 0
        $usersWithTimeIn = Attendance::with('user')->where('status', 1)->where('date', $date)->get();

        dd($usersWithTimeIn);

        // Fetch the latest QR code created today
        $QrCode = QrCode::whereDate('created_at', $date)->latest()->first();

        return inertia('Dashboard', [
            'qrcode' => $QrCode,
            'users' => $usersWithTimeIn,
        ]);
    }

    public function scanner()
    {
        $user = auth()->user();
        return inertia('Modules/Attendance/Scanner', [
            'users' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->route('attendance.scanner')->with('error', $validator->errors());
        }

        $currentDate = Carbon::today()->toDateString();

        if ($request->date !== $currentDate) {
            return redirect()->route('attendance.scanner')->with('error', 'QR Code Expired.');
        }

        $currentTime = now(); // This will use the configured timezone (Asia/Manila)
        $currentHour = $currentTime->hour;
        $formattedTime = $currentTime->format('H:i:s A');

        $attendance = Attendance::firstOrNew([
            'user_id' => $request->user_id,
            'date' => $request->date,
        ]);

        $formattedTime = $currentTime->format('h:i:s A'); // Format to 12-hour format

        if ($currentHour < 12) {
            // Morning
            if (is_null($attendance->am_time_in)) {
                $attendance->am_time_in = $formattedTime;
                $attendance->status = 1;
            } elseif (is_null($attendance->am_time_out) && $attendance->am_time_in->diffInMinutes($currentTime) >= 15) {
                $attendance->am_time_out = $formattedTime;
                $attendance->status = 0;
            }
        } else {
            // Afternoon
            if (is_null($attendance->am_time_in) && is_null($attendance->am_time_out) && $currentHour >= 12) {
                // If both am_time_in and am_time_out are null and current time is past 12 PM, store time in pm_time_in
                $attendance->pm_time_in = $formattedTime;
                $attendance->status = 1;
            } elseif (is_null($attendance->pm_time_in)) {
                if (!is_null($attendance->am_time_in) && $currentHour >= 12 && $currentHour < 13) {
                    // If am_time_in is not null and current time is past 12 PM but before 1 PM, store time in am_time_out
                    $attendance->am_time_out = $formattedTime;
                    $attendance->status = 0;
                } elseif (is_null($attendance->am_time_out) && $currentHour >= 13) {
                    // If current time is past 1 PM and am_time_out is still null, store time in pm_time_in
                    $attendance->pm_time_in = $formattedTime;
                    $attendance->status = 1;
                }
            } elseif (is_null($attendance->pm_time_out) && $attendance->pm_time_in->diffInMinutes($currentTime) >= 15) {
                $attendance->pm_time_out = $formattedTime;
                $attendance->status = 0;
            }
        }

        // Automatically set pm_time_in to current time if am_time_out is null and the current time is past 1 PM
        if (is_null($attendance->am_time_out) && $currentHour >= 13 && is_null($attendance->pm_time_in)) {
            $attendance->pm_time_in = $formattedTime;
        }

        // Automatically set pm_time_out to 5 PM if it is missing and the current time is past 5 PM
        if (is_null($attendance->pm_time_out) && $currentHour >= 17) {
            $attendance->pm_time_out = Carbon::createFromTime(17, 0, 0)->format('h:i:s A');
        }


        // Calculate undertime
        $morningHours = 0;
        $afternoonHours = 0;

        if ($attendance->am_time_in && $attendance->am_time_out) {
            $am_start = Carbon::createFromTime(8, 0, 0);
            $am_end = Carbon::createFromTime(12, 0, 0);
            $morningHours = $attendance->am_time_in->max($am_start)->diffInMinutes($attendance->am_time_out->min($am_end)) / 60;
        }

        if ($attendance->pm_time_in && $attendance->pm_time_out) {
            $pm_start = Carbon::createFromTime(13, 0, 0);
            $pm_end = Carbon::createFromTime(17, 0, 0);
            $afternoonHours = $attendance->pm_time_in->max($pm_start)->diffInMinutes($attendance->pm_time_out->min($pm_end)) / 60;
        }

        $totalHours = $morningHours + $afternoonHours;
        $undertime = max(0, 8 - $totalHours);

        $attendance->hours_under_time = floor($undertime);
        $attendance->minutes_under_time = ($undertime - floor($undertime)) * 60;

        $attendance->save();

        return redirect()->route('dashboard')->with('message', 'Attendance Successfull Saved.' . $currentTime->hour);
    }
}
