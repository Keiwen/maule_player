<?php

namespace App\Security;


use App\Controller\Mail\MailSender;
use App\Entity\User;
use Symfony\Component\HttpKernel\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailUserVerificator
{
    protected $router;
    protected $userTokenGenerator;
    protected $translator;
    protected $mailSender;

    protected $urlSignSecretEmailVerification = 'e*3Ba_eM41lV3r1ficaTion';
    protected $urlSignSecretResetPassword = '5Sv@u_R3setp4Bw0rd';

    public function __construct(RouterInterface $router, UserTokenGenerator $userTokenGenerator, TranslatorInterface $translator, MailSender $mailSender)
    {
        $this->router = $router;
        $this->userTokenGenerator = $userTokenGenerator;
        $this->translator = $translator;
        $this->mailSender = $mailSender;
    }

    /**
     * @param User $user
     * @return string
     */
    public function generateEmailVerificationLink(User $user): string
    {
        $uri = $this->router->generate('user_verify_email', [
            'mail' => $user->getEmail(),
            'token' => $this->generateUserToken($user)
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $uri = (new UriSigner($this->urlSignSecretEmailVerification))->sign($uri);

        return $uri;
    }

    /**
     * @param User $user
     * @return string
     */
    public function generateResetPasswordLink(string $email): string
    {
        $uri = $this->router->generate('user_reset_password', [
            'mail' => $email,
            'date' => date('Y-m-d'),
            'token' => $this->generateEmailToken($email)
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $uri = (new UriSigner($this->urlSignSecretResetPassword))->sign($uri);

        return $uri;
    }


    public function generateUserToken(User $user): string
    {
        return $this->userTokenGenerator->generateToken($user, $this->urlSignSecretEmailVerification);
    }

    public function generateEmailToken(string $email): string
    {
        return $this->userTokenGenerator->generateEmailToken($email, $this->urlSignSecretResetPassword);
    }

    public function checkLinkSignature(string $uri): bool
    {
        return (new UriSigner($this->urlSignSecretEmailVerification))->check($uri);
    }

    public function checkResetLinkSignature(string $uri): bool
    {
        return (new UriSigner($this->urlSignSecretResetPassword))->check($uri);
    }


    public function sendVerificationMail(User $user): bool
    {
        return $this->mailSender->sendMail(
            $user->getEmail(),
            $this->translator->trans('verify_email.title', [], 'email'),
            'verify_email',
            [
                'url' => $this->generateEmailVerificationLink($user)
            ]
        );
    }

    public function sendResetMail(string $email): bool
    {
        return $this->mailSender->sendMail(
            $email,
            $this->translator->trans('reset_password.title', [], 'email'),
            'reset_password',
            [
                'url' => $this->generateResetPasswordLink($email)
            ]
        );
    }

}
