<?php

declare(strict_types = 1);

namespace App\Http\Requests\Auth;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class LoginData extends Data
{
    public function __construct(
        #[Required]
        #[Email]
        #[Exists('users', 'email')]
        public string $email,
        #[Required]
        public string $password,
        #[Required]
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
