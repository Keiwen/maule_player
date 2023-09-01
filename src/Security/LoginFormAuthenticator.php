<?php

namespace App\Security;

use App\Form\AdminLoginFormType;
use Keiwen\Cacofony\EntitiesManagement\EntityRegistry;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'user_login';

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;
    /** @var FormFactoryInterface */
    protected $formFactory;
    /** @var EntityRegistry */
    protected $entityRegistry;

    public function __construct(UrlGeneratorInterface $urlGenerator, FormFactoryInterface $formFactory, EntityRegistry $entityRegistry)
    {
        $this->urlGenerator = $urlGenerator;
        $this->formFactory = $formFactory;
        $this->entityRegistry = $entityRegistry;
    }

    public function authenticate(Request $request): PassportInterface
    {
        $form = $this->formFactory->create(AdminLoginFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return new Passport(
                new UserBadge('maule_admin'),
                new PasswordCredentials($form->get('plainPassword')->getData()), []);
        }

        throw new AuthenticationException('form not submitted');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('index'), Response::HTTP_SEE_OTHER);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
