<?php

namespace App\Models;

class User extends \Core\Model
{
    static protected string|null $tableName = 'users';

    public string|null $email, $password, $token, $created_at, $token_expired_at = null;

    public function getUserInfo(): array
    {
        return [
            'email' => $this->email,
            'token' => $this->token
        ];
    }
}