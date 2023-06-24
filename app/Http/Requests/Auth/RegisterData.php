<?php

declare(strict_types = 1);

namespace App\Http\Requests\Auth;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

final class RegisterData extends Data
{
    public function __construct(
        #[Required]
        #[Max(255)]
        public string $name,
        #[Required]
        #[Email]
        #[Unique('users', 'email')]
        public string $email,
        #[Required]
        #[Min(8)]
        #[Confirmed]
        public string $password,
        #[Required]
        #[Min(8)]
        #[MapName('password_confirmation')]
        public string $passwordConfirmation,
        #[Required]
        #[MapName('token-name')]
        public string $tokenName,
    ) {
    }
}
