<?php

namespace App\Console\Commands;

use App\Events\RefreshUser;
use App\Models\User;
use App\Models\qrcode;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DispatchRefreshUserEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dispatch-refresh-user-event';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch the RefreshUser event';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fetch the QR code
        $date = Carbon::today()->toDateString();
        $qrCode = QrCode::whereDate('created_at', $date)->latest()->first();

        if ($qrCode) {
            Log::info('QR Code fetched', ['qr_code' => $qrCode]);

            // Find the admin user
            $admins = User::role(['superadmin', 'administrator', 'timekeeper'])->get();

            if ($admins->isNotEmpty()) {
                broadcast(new RefreshUser($admins->all()));
            } else {
                Log::error('No admin user found');
            }
        } else {
            $this->error('No QR code found for today.');
        }
    }
}
