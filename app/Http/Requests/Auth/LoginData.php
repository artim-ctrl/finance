<?php

namespace App\Http\Requests\Auth;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class LoginData extends Data
{
    public function __construct(
        #[Email]
        #[Exists('users', 'email')]
        public string $email,
        public string $password,
        #[MapName('token-name')]
        public string $tokenName,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'email.exists' => 'There is no user with this email',
        ];
    }
}
