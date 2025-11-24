<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendVerificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1; // Only try once
    public $timeout = 10; // 10 second timeout

    protected $email;
    protected $verificationUrl;

    public function __construct($email, $verificationUrl)
    {
        $this->email = $email;
        $this->verificationUrl = $verificationUrl;
    }

    public function handle(): void
    {
        try {
            Log::info('[EMAIL JOB] Attempting to send verification email', ['to' => $this->email]);
            
            Mail::raw(
                "Welcome to Bandmate!\n\n" .
                "Please click the link below to verify your email address:\n\n" .
                $this->verificationUrl . "\n\n" .
                "This link will expire in 24 hours.\n\n" .
                "If you didn't create an account, please ignore this email.\n\n" .
                "Best regards,\nThe Bandmate Team",
                function ($message) {
                    $message->to($this->email)
                            ->subject('Verify Your Bandmate Account');
                }
            );
            
            Log::info('[EMAIL JOB] Email sent successfully', ['to' => $this->email]);
            
        } catch (\Exception $e) {
            Log::error('[EMAIL JOB] Failed to send email', [
                'to' => $this->email,
                'error' => $e->getMessage()
            ]);
            // Don't throw - let it fail silently
        }
    }
}
