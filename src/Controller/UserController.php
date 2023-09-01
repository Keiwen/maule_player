<?php

namespace App\Controller;

use App\Form\AdminLoginFormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @package App\Controller
 * @Route("/user", name="user_")
 */
class UserController extends AbstractAppController
{

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(AdminLoginFormType::class);
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if (!empty($error)) {
            $this->addFlash('error', $translator->trans($error->getMessageKey(), $error->getMessageData(), 'security'));
        }

        return $this->renderTemplate(array(
            'loginForm' => $form->createView(),
        ));
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

}
