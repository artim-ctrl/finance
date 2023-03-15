<?php

namespace App\Http\Requests\Auth;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class RegisterData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,
        #[Email]
        public string $email,
        #[Min(8)]
        #[Confirmed]
        public string $password,
        #[Min(8)]
        #[MapName('password_confirmation')]
        public string $passwordConfirmation,
        #[MapName('token-name')]
        public string $tokenName,
    ) {
    }
}
