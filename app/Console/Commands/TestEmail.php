<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmail extends Command
{
    protected $signature = 'email:test {recipient}';
    protected $description = 'Test email sending configuration';

    public function handle()
    {
        $recipient = $this->argument('recipient');
        
        $this->info('Testing email configuration...');
        $this->info('MAIL_MAILER: ' . config('mail.default'));
        $this->info('MAIL_HOST: ' . config('mail.mailers.smtp.host'));
        $this->info('MAIL_PORT: ' . config('mail.mailers.smtp.port'));
        $this->info('MAIL_USERNAME: ' . config('mail.mailers.smtp.username'));
        $this->info('MAIL_FROM: ' . config('mail.from.address'));
        $this->line('');
        
        try {
            $this->info("Sending test email to: {$recipient}");
            
            Mail::raw('This is a test email from Bandmate. If you receive this, email is working!', function ($message) use ($recipient) {
                $message->to($recipient)
                        ->subject('Test Email - Bandmate');
            });
            
            $this->info('✓ Email sent successfully!');
            $this->line('');
            $this->info('Check your inbox (and spam folder) for the test email.');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('✗ Failed to send email!');
            $this->error('Error: ' . $e->getMessage());
            Log::error('Test email failed', [
                'recipient' => $recipient,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }
}
