<?php

namespace App\Providers;

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     * Uses `resolving` so the DB is ONLY queried when mail is actually needed,
     * preventing unnecessary overhead on normal web requests.
     * This also works correctly with Queue Workers — each Job gets fresh config
     * without needing `php artisan queue:restart`.
     */
    public function boot(): void
    {
        $this->app->resolving(MailManager::class, function (MailManager $manager) {
            $settings = Cache::rememberForever('mail_settings', function () {
                try {
                    return \App\Models\Setting::where('group', 'mail')
                        ->pluck('value', 'key')
                        ->toArray();
                } catch (\Exception) {
                    // Table may not exist yet during fresh install / migrations
                    return [];
                }
            });

            if (empty($settings) || empty($settings['mail_host'] ?? null)) {
                return; // Fall back to .env values gracefully
            }

            // Safely decrypt password
            $password = $settings['mail_password'] ?? '';
            if (!empty($password)) {
                try {
                    $password = Crypt::decryptString($password);
                } catch (\Exception) {
                    // If decryption fails (plain-text stored), use as-is
                }
            }

            $driver = $settings['mail_driver'] ?? 'smtp';

            Config::set('mail.default', $driver);

            Config::set('mail.from', [
                'address' => $settings['mail_from_address'] ?? Config::get('mail.from.address'),
                'name'    => $settings['mail_from_name']    ?? Config::get('mail.from.name'),
            ]);

            if ($driver === 'smtp') {
                Config::set('mail.mailers.smtp', array_merge(
                    Config::get('mail.mailers.smtp', []),
                    [
                        'host'       => $settings['mail_host']       ?? Config::get('mail.mailers.smtp.host'),
                        'port'       => (int) ($settings['mail_port'] ?? Config::get('mail.mailers.smtp.port')),
                        'username'   => $settings['mail_username']   ?? Config::get('mail.mailers.smtp.username'),
                        'password'   => $password                    ?: Config::get('mail.mailers.smtp.password'),
                        'encryption' => $settings['mail_encryption'] ?? Config::get('mail.mailers.smtp.encryption'),
                    ]
                ));
            }
        });
    }
}
