<?php

namespace App\Security;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AppUserAdmin implements UserInterface, PasswordAuthenticatedUserInterface
{
    protected $passwordHasher = null;

    protected $hashedPassword = '';
    protected $plainPassword = '';

    public function __construct(PasswordHasherFactoryInterface $hasherFactory, string $plainPassword = '')
    {
        $this->passwordHasher = $hasherFactory->getPasswordHasher(self::class);
        $this->plainPassword = $plainPassword;
    }

    public function getPassword(): ?string
    {
        if (!empty($this->hashedPassword)) return $this->hashedPassword;
        $hash = $this->passwordHasher->hash($this->plainPassword);
        return $hash;
    }

    public function getSalt()
    {
        return '';
    }

    public function eraseCredentials()
    {
    }


    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function getRoles()
    {
        return array('ROLE_ADMIN');
    }

    public function getUsername()
    {
        return 'maule_admin';
    }

    public function savePassword(string $hashedPassword)
    {
        $this->hashedPassword = $hashedPassword;
    }
}
