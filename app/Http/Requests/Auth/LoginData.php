<?php

namespace App\Http\Requests\Auth;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Data;

class LoginData extends Data
{
    public function __construct(
        #[Email]
        public string $email,
        public string $password,
        #[MapName('token-name')]
        public string $tokenName,
    ) {
    }
}
