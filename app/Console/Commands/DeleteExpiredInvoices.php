<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Invoice\App\Models\Invoice;
use Modules\QRCode\App\Models\QRCode;

class DeleteExpiredInvoices extends Command
{
    protected $signature = 'invoices:delete-expired';
    protected $description = 'Delete invoices and QR codes that have passed their scheduled deletion date';

    public function handle(): void
    {
        $invoiceCount = Invoice::expired()->delete();
        $this->info("Deleted {$invoiceCount} expired invoice(s).");

        $qrCount = QRCode::expired()->delete();
        $this->info("Deleted {$qrCount} expired QR code(s).");
    }
}
