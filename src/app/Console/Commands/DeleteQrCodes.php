<?php

namespace App\Console\Commands;

use App\Models\qrcode;
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
        // Delete all QR codes that were created more than 1 day ago
        $qrcodes = qrcode::where('created_at', '<', now()->subDays(1))->get();

        foreach ($qrcodes as $qrcode) {
            $qrcode->delete();
        }

        $this->info('QR codes older than 1 day have been deleted.');
    }
}
