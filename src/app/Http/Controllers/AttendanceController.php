<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\qrcode;
use App\Models\attendance;
use App\Events\RefreshUser;
use App\Models\User;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $users = User::role('ojt')->when($request->search, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        })->with('attendances')->paginate(10);

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

        // Fetch users who have a time-in record for today with status 1
        $usersWithTimeIn = attendance::with('user')->where('status', 1)->where('date', $date)->get();
        $user = attendance::with('user')->where('user_id', auth()->user()->id)->orderBy('date', 'asc')->get();



        // Fetch the latest QR code created today
        $QrCode = QrCode::whereDate('created_at', $date)->latest()->first();
        return inertia('Dashboard', [
            'qrcode' => $QrCode,
            'users' => $usersWithTimeIn,
            'user' => $user
        ]);
    }

    public function scanner()
    {
        $user = auth()->user();
        return inertia('Modules/Attendance/Scanner', [
            'users' => $user,
        ]);
    }

    public function fetchUser()
    {
        $date = Carbon::today()->toDateString();

        // Fetch users who have a time-in record for today with status 0
        $usersWithTimeIn = attendance::with('user')->where('status', 1)->where('date', $date)->get();

        return response()->json($usersWithTimeIn);
    }

    public function store(Request $request)
    {

        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('attendance.scanner')->with('error', $validator->errors());
        }

        $currentDate = Carbon::today()->toDateString();

        $latestQRCode = qrcode::whereDate('created_at', $currentDate)->latest()->first();

        if ($latestQRCode->qr_code !== $request->date) {
            return redirect()->route('attendance.scanner')->with('error', 'Invalid QR Code.');
        }

        $qrCodeContent = $request->date;
        $dateParts = explode('-', $qrCodeContent);
        if (count($dateParts) < 3) {
            return redirect()->route('attendance.scanner')->with('error', 'Invalid QR Code format.');
        }
        $currentDates = implode('-', array_slice($dateParts, 0, 3));

        if ($currentDates !== $currentDate) {
            return redirect()->route('attendance.scanner')->with('error', 'QR Code Expired.');
        }

        $currentTime = now(); // This will use the configured timezone (Asia/Manila)
        $currentHour = $currentTime->hour;
        $formattedTime = $currentTime->format('H:i:s A');

        $attendance = Attendance::firstOrNew([
            'user_id' => $request->user_id,
            'date' => $currentDate,
        ]);

        $formattedTime = $currentTime->format('h:i:s A'); // Format to 12-hour format

        // Check if all time-in fields are empty and it's already 5 PM or later
        if (is_null($attendance->am_time_in) && is_null($attendance->am_time_out) && is_null($attendance->pm_time_in) && is_null($attendance->pm_time_out) && $currentHour >= 17) {
            return redirect()->route('dashboard')->with('error', 'Cannot insert data after 5 PM if no time-in records exist.');
        }

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
            if (is_null($attendance->pm_time_in)) {
                if (is_null($attendance->am_time_in) && $currentHour >= 12 && $currentHour < 13 && is_null($attendance->am_time_out)) {
                    // If am_time_in is not null and current time is past 12 PM but before 1 PM, store time in am_time_out
                    $attendance->pm_time_in = $formattedTime;
                    $attendance->status = 1;
                } elseif (!is_null($attendance->am_time_in) && $currentHour >= 12 && $currentHour < 13 && is_null($attendance->am_time_out)) {
                    // If am_time_in is not null and current time is past 12 PM but before 1 PM, store time in am_time_out
                    $attendance->am_time_out = $formattedTime;
                    $attendance->status = 0;
                } elseif (!is_null($attendance->am_time_out) && $attendance->am_time_out->diffInMinutes($currentTime) >= 15) {
                    // If am_time_out is not null and there is a 15-minute interval, store time in pm_time_in
                    $attendance->pm_time_in = $formattedTime;
                    $attendance->status = 1;
                } elseif (is_null($attendance->am_time_out) && $currentHour >= 13) {
                    // If current time is past 1 PM and am_time_out is still null, store time in pm_time_in
                    $attendance->pm_time_in = $formattedTime;
                    $attendance->status = 1;
                }
            } elseif (!is_null($attendance->pm_time_in) && is_null($attendance->pm_time_out) && $attendance->pm_time_in->diffInMinutes($currentTime) >= 15) {
                // If pm_time_in is not null and there is a 15-minute interval, store time in pm_time_out
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

        broadcast(new RefreshUser('new data received...'));

        return redirect()->route('dashboard')->with('message', 'Attendance Successfull Saved.');
    }


    public function profile($hashedId)
    {
        $id = Hashids::decode($hashedId)[0];
        $user = User::with('attendances')->findOrFail($id);

        $user->hashed_id = $hashedId;
        $user->attendances->transform(function ($attendance) {
            $attendance->hashed_id = Hashids::encode($attendance->id);
            return $attendance;
        });

        return inertia('Modules/Attendance/Profile', [
            'users' => $user,
        ]);
    }


    public function editProfile($hashedId)
    {
        $id = Hashids::decode($hashedId)[0];
        $user = attendance::with('user')->findOrFail($id);


        $user->hashed_id = Hashids::encode($user->user_id);

        return inertia('Modules/Attendance/EditUserLog', [
            'dtr' => $user,
            'hashedId' => $hashedId,
        ]);
    }


    public function updateLog(Request $request, $hashedId, $hashed_id)
    {
        $id = Hashids::decode($hashedId)[0];
        $attendance = attendance::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'am_time_in' => 'nullable',
            'am_time_out' => 'nullable',
            'pm_time_in' => 'nullable',
            'pm_time_out' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors());
        }

        $attendance->am_time_in = $request->am_time_in ? Carbon::parse($request->am_time_in)->format('Y-m-d H:i:s') : null;
        $attendance->am_time_out = $request->am_time_out ? Carbon::parse($request->am_time_out)->format('Y-m-d H:i:s') : null;
        $attendance->pm_time_in = $request->pm_time_in ? Carbon::parse($request->pm_time_in)->format('Y-m-d H:i:s') : null;
        $attendance->pm_time_out = $request->pm_time_out ? Carbon::parse($request->pm_time_out)->format('Y-m-d H:i:s') : null;

        // Calculate undertime
        $morningMinutes = 0;
        $afternoonMinutes = 0;

        if ($attendance->am_time_in && $attendance->am_time_out) {
            $am_start = Carbon::createFromTime(8, 0, 0, 'Asia/Manila');
            $am_end = Carbon::createFromTime(12, 0, 0, 'Asia/Manila');
            $am_in = Carbon::parse($attendance->am_time_in, 'Asia/Manila')->max($am_start);
            $am_out = Carbon::parse($attendance->am_time_out, 'Asia/Manila')->min($am_end);
            if ($am_in < $am_out) {
                $morningMinutes = $am_in->diffInMinutes($am_out);
            }
        }

        if ($attendance->pm_time_in && $attendance->pm_time_out) {
            $pm_start = Carbon::createFromTime(13, 0, 0, 'Asia/Manila');
            $pm_end = Carbon::createFromTime(17, 0, 0, 'Asia/Manila');
            $pm_in = Carbon::parse($attendance->pm_time_in, 'Asia/Manila')->max($pm_start);
            $pm_out = Carbon::parse($attendance->pm_time_out, 'Asia/Manila')->min($pm_end);
            if ($pm_in < $pm_out) {
                $afternoonMinutes = $pm_in->diffInMinutes($pm_out);
            }
        }

        $totalMinutes = $morningMinutes + $afternoonMinutes;
        $requiredMinutes = 8 * 60; // 8 hours in minutes
        $undertimeMinutes = max(0, $requiredMinutes - $totalMinutes);

        $attendance->hours_under_time = floor($undertimeMinutes / 60);
        $attendance->minutes_under_time = $undertimeMinutes % 60;

        $attendance->save();

        return redirect()->route('attendance.profile', $hashed_id)->with('message', 'Attendance Log Updated.');
    }

    public function destroy($hashedId, $hashed_id)
    {
        $id = Hashids::decode($hashedId)[0];
        $attendance = attendance::findOrFail($id);
        $attendance->delete();

        broadcast(new RefreshUser('new data received...'));

        return redirect()->route('attendance.profile', $hashed_id)->with('message', 'Attendance Log Deleted.');
    }
}
