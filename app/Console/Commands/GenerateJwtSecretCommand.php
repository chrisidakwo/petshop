<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Encryption\Encrypter;

class GenerateJwtSecretCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:secret';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate JWT secret';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $secret = base64_encode(
            // @phpstan-ignore-next-line
            Encrypter::generateKey($this->laravel['config']['app.cipher'])
        );

        // @phpstan-ignore-next-line
        $currentSecret = $this->laravel['config']['auth.jwt.secret'];

        if (strlen($currentSecret) > 0 && ! $this->confirmToProceed()) {
            return;
        }

        $replaced = preg_replace(
            $this->secretReplacementPattern(),
            'JWT_SECRET='.$secret,
            $input = file_get_contents($this->laravel->environmentFilePath()) // @phpstan-ignore-line
        );

        if ($replaced === $input || $replaced === null) {
            $this->error('Unable to set JWT secret. No JWT_SECRET variable was found in the .env file.');

            return;
        }

        file_put_contents($this->laravel->environmentFilePath(), $replaced);

        $this->components->info('JWT secret set successfully.');
    }

    /**
     * Get a regex pattern that will match env JWT_SECRET with any random key.
     */
    protected function secretReplacementPattern(): string
    {
        // @phpstan-ignore-next-line
        $escaped = preg_quote('='.$this->laravel['config']['auth.jwt.secret'], '/');

        return "/^JWT_SECRET{$escaped}/m";
    }
}
