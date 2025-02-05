<?php

namespace App\Console\Commands;

use App\Models\qrcode;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:delete-qr-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete QR Codes older than 1 day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::today();

        $qrcodes = qrcode::where('created_at', '<', $date)->get();
        
        foreach ($qrcodes as $qrcode) {
            $qrcode->delete();
        }
        
        $this->info('QR codes older than 1 day have been deleted.');
    }
}
