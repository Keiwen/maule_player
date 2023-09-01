<?php

namespace App\Security;

use App\Entity\User;

class UserTokenGenerator
{

    public function generateToken(User $user, string $secret, array $otherData = array()): string
    {
        $data = [
            'id' => $user->getId(),
            'email' => $user->getEmail()
        ];
        $data = array_merge($otherData, $data);
        return base64_encode(hash_hmac('sha256', json_encode($data), $secret, true));
    }

    public function generateEmailToken(string $email, string $secret, array $otherData = array()): string
    {
        $data = [
            'email' => $email
        ];
        $data = array_merge($otherData, $data);
        return base64_encode(hash_hmac('sha256', json_encode($data), $secret, true));
    }

}
