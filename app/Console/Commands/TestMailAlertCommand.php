<?php

namespace App\Console\Commands;

use App\Mail\NewContactMessageAlert;
use App\Mail\NewOrderAdminAlert;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailAlertCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test-alert {email? : Destination email address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email alert to store admin (daisyjewelers940@gmail.com)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $targetEmail = $this->argument('email') 
            ?: env('ADMIN_ALERT_EMAIL', Setting::get('admin_alert_email', 'daisyjewelers940@gmail.com'));

        $this->info("Sending test alert to: {$targetEmail}");
        $this->line("Current mail driver: " . config('mail.default'));

        $sampleOrder = Order::latest()->with('items')->first();

        if ($sampleOrder) {
            try {
                Mail::to($targetEmail)->send(new NewOrderAdminAlert($sampleOrder));
                $this->info("✅ SUCCESS: Order alert sent for order #{$sampleOrder->order_number}!");
            } catch (\Throwable $e) {
                $this->error("❌ ERROR sending order alert: " . $e->getMessage());
                return Command::FAILURE;
            }
        } else {
            $this->warn("No order found to test with.");
        }

        return Command::SUCCESS;
    }
}
