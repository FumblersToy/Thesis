<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;

class SendVerificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1; // Only try once
    public $timeout = 10; // 10 second timeout

    protected $email;
    protected $code;

    public function __construct($email, $code)
    {
        $this->email = $email;
        $this->code = $code;
    }

    public function handle(): void
{
    try {
        Log::info('[EMAIL JOB] Attempting to send via Brevo API', ['to' => $this->email]);
        
        $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));
        $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(
            new \GuzzleHttp\Client(),
            $config
        );
        
        $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail([
            'subject' => 'Your Bandmate Verification Code',
            'sender' => ['email' => 'kadmielchunks@gmail.com', 'name' => 'Bandmate'],
            'to' => [['email' => $this->email]],
            'textContent' => "Welcome to Bandmate!\n\n" .
                "Your verification code is:\n\n" .
                $this->code . "\n\n" .
                "This code will expire in 30 minutes.\n\n" .
                "If you didn't create an account, please ignore this email.\n\n" .
                "Best regards,\nThe Bandmate Team"
        ]);
        
        $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
        
        Log::info('[EMAIL JOB] Email sent successfully via Brevo API', [
            'to' => $this->email,
            'message_id' => $result->getMessageId()
        ]);
        
    } catch (\Exception $e) {
        Log::error('[EMAIL JOB] Failed to send email via Brevo API', [
            'to' => $this->email,
            'error' => $e->getMessage()
        ]);
        throw $e; // Retry the job
    }
}
}
