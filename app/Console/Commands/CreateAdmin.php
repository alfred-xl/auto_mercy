<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

#[Signature('app:create-admin')]
#[Description('Create an Auto Mercy administrator interactively')]
class CreateAdmin extends Command
{
    public function handle(): int
    {
        $data = [
            'name' => trim((string) $this->ask('Name')),
            'email' => mb_strtolower(trim((string) $this->ask('Email address'))),
            'password' => (string) $this->secret('Password'),
            'password_confirmation' => (string) $this->secret('Confirm password'),
        ];
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $this->info("Administrator {$data['email']} was created successfully.");

        return self::SUCCESS;
    }
}
