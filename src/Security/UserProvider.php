<?php

namespace App\Security;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{

    protected $hasherFactory;
    protected $userPlainPasswords = array();

    public function __construct(PasswordHasherFactoryInterface $hasherFactory, array $userPlainPasswords = array())
    {
        $this->hasherFactory = $hasherFactory;
        $this->userPlainPasswords = $userPlainPasswords;
    }

    protected function getPlainPasswordForUser(string $userIdentifier)
    {
        return $this->userPlainPasswords[$userIdentifier] ?? '';
    }

    /**
     * The loadUserByIdentifier() method was introduced in Symfony 5.3.
     * In previous versions it was called loadUserByUsername()
     *
     * Symfony calls this method if you use features like switch_user
     * or remember_me. If you're not using these features, you do not
     * need to implement this method.
     *
     * @throws UserNotFoundException if the user is not found
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // Load a User object from your data source or throw UserNotFoundException.
        // The $identifier argument is whatever value is being returned by the
        // getUserIdentifier() method in your User class.

        $plainPassword = $this->getPlainPasswordForUser($identifier);
        if (empty($plainPassword)) {
            throw new \LogicException(sprintf(
                'No password defined for user %s. Did you forget to initialize USER_PASSWORD_%s parameter in env file?',
                $identifier,
                strtoupper($identifier)
            ));
        }

        return new AppUserAdmin($this->hasherFactory, $plainPassword);
    }

    public function loadUserByUsername(string $username)
    {
        return $this->loadUserByIdentifier($username);
    }


    /**
     * Refreshes the user after being reloaded from the session.
     *
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     *
     * If your firewall is "stateless: true" (for a pure API), this
     * method is not called.
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        // Return a User object after making sure its data is "fresh".
        // Or throw a UserNotFoundException if the user no longer exists.

        if ($user instanceof AppUserAdmin) {
            // we used this to store hashed password, otherwise we will re-hash it
            // and then it will invalidate user in
            // Symfony\Component\Security\Http\Firewall\ContextListener::hasUserChanged
            $user->savePassword($user->getPassword());
        }

        return $user;
    }

    /**
     * Tells Symfony to use this provider for this User class.
     */
    public function supportsClass(string $class): bool
    {
        return AppUserAdmin::class === $class || is_subclass_of($class, AppUserAdmin::class);
    }

    /**
     * Upgrades the hashed password of a user, typically for using a better hash algorithm.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
    }
}
