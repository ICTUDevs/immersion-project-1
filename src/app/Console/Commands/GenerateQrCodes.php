<?php

namespace App\Console\Commands;

use App\Models\qrcode as qrcodeModel;
use Illuminate\Console\Command;

class GenerateQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:qr-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR Codes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Generate a random string
        $randomString = $this->generateRandomString(10); // Adjust the length as needed

        // Combine the current date with the random string
        $qrCodeContent = now()->toDateString() . '-' . $randomString;


        qrcodeModel::create([
            'type' => now()->toDateString(),
            'qr_code' => $qrCodeContent,
        ]);

        $this->info('QR codes generated successfully.');
    }

    /**
     * Generate a random string of the specified length.
     *
     * @param int $length
     * @return string
     */
    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
