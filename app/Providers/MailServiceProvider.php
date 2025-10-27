<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;

class MailServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if (config('mail.default') === 'smtp') {
            $this->app->extend('mail.manager', function ($manager) {
                $manager->extend('smtp', function (array $config) {
                    $factory = new EsmtpTransportFactory();
                    
                    $dsn = new Dsn(
                        'smtp',
                        config('mail.mailers.smtp.host'),
                        config('mail.mailers.smtp.username'),
                        config('mail.mailers.smtp.password'),
                        config('mail.mailers.smtp.port'),
                        [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true,
                        ]
                    );
                    
                    return $factory->create($dsn);
                });
                
                return $manager;
            });
        }
    }
}

