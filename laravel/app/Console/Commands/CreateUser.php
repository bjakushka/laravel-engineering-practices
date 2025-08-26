<?php

namespace App\Console\Commands;

use App\Services\AuthService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {name} {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user with hashed password';

    public function __construct(
        readonly private AuthService $authService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return Command::FAILURE;
        }

        $user = $this->authService->register([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $this->info("user created successfully!");
        $this->info("id: {$user->id}");
        $this->info("name: {$user->name}");
        $this->info("email: {$user->email}");

        return Command::SUCCESS;
    }
}
