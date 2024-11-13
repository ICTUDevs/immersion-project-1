<?php

namespace App\Console\Commands;

use App\Models\qrcode as qrcodeModel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Console\Command;
use Endroid\QrCode\QrCode;

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
        $types = ['am', 'pm'];
        $writer = new PngWriter();

        foreach ($types as $type) {
            $qrCode = new QrCode($type . '-' . now()->toDateString());
            $qrCodeData = $writer->write($qrCode)->getString();
            $encodedQrCodeData = base64_encode($qrCodeData);

            qrcodeModel::create([
                'type' => $type,
                'code' => $encodedQrCodeData,
            ]);
        }

        $this->info('QR codes generated successfully.');
    }
}
