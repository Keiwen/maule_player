<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\LoginFormType;
use App\Form\RegistrationFormType;
use App\Form\RepeatPasswordFormType;
use App\Repository\UserRepository;
use App\Security\EmailUserVerificator;
use App\Security\LoginFormAuthenticator;
use Keiwen\Utils\Sanitize\StringSanitizer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Keiwen\Cacofony\Security\Annotation\RestrictToRole;

/**
 * @package App\Controller
 * @Route("/user", name="user_")
 */
class UserController extends AbstractAppController
{

    protected $userPasswordHasher;
    protected $userAuthenticator;
    protected $loginFormAuthenticator;
    protected $emailUserVerificator;

    public function __construct(
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $loginFormAuthenticator,
        EmailUserVerificator $emailUserVerificator)
    {
        parent::__construct();
        $this->userPasswordHasher = $userPasswordHasher;
        $this->userAuthenticator = $userAuthenticator;
        $this->loginFormAuthenticator = $loginFormAuthenticator;
        $this->emailUserVerificator = $emailUserVerificator;
    }


    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->registerUser($user, $form);
        }

        return $this->renderTemplate([
            'registrationForm' => $form->createView(),
        ]);
    }


    /**
     * @param User $user
     * @param RegistrationFormType $form
     * @return mixed
     */
    protected function registerUser(User $user, FormInterface $form): Response
    {
        // encode the plain password
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $form->get('repeatPassword')->get('plainPassword')->getData()
            )
        );

        $this->getEntityRegistry()->saveObject($user);
        // do anything else you need here, like send an email
        $sent = $this->emailUserVerificator->sendVerificationMail($user);

        // instead of redirection, work on login process
        return $this->userAuthenticator->authenticateUser($user, $this->loginFormAuthenticator, $this->getRequest());
    }


    /**
     * @RestrictToRole("user")
     * @Route("/sendVerificationMail", name="send_verification_mail")
     */
    public function sendVerificationMail(TranslatorInterface $translator): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $sent = $this->emailUserVerificator->sendVerificationMail($user);
        if ($sent) {
            $this->addFlash('success', $translator->trans('email_sent_ok'));
        } else {
            $this->addFlash('error', $translator->trans('email_sent_ko'));
        }
        return $this->redirectToRoute('user_my_account', ['tab' => 'account'], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/verify_email", name="verify_email")
     */
    public function verifyEmail(Request $request, TranslatorInterface $translator): Response
    {

        $currentUri = $request->getUri();

        if ($this->emailUserVerificator->checkLinkSignature($currentUri)) {
            $email = $request->get('mail', '');
            $stringSanitizer = new StringSanitizer();
            $email = $stringSanitizer->get($email, StringSanitizer::FILTER_MAIL);

            /** @var UserRepository $userRepository */
            $userRepository = $this->getRepository(User::class);
            $userByMail = $userRepository->findBy(['email' => $email]);
            $userByMail = reset($userByMail);

            $serverToken = $this->emailUserVerificator->generateUserToken($userByMail);
            $clientToken = $request->get('token', '');
            if ($serverToken == $clientToken) {
                $userByMail->setEmailVerified(true);
                $this->getEntityRegistry()->saveObject($userByMail);

                $this->addFlash('success', $translator->trans('user.email_verification_ok'));
                return $this->renderTemplate();
            }
        }

        $this->addFlash('error', $translator->trans('user.email_verification_ko'));
        return $this->renderTemplate([
        ]);
    }

    /**
     * @Route("/forgotPassword", name="forgot_password")
     */
    public function forgotPassword(Request $request, AuthenticationUtils $authenticationUtils, TranslatorInterface $translator): Response
    {
        $lastUsername = $authenticationUtils->getLastUsername();
        $form = $this->createFormBuilder(['email' => $lastUsername])
            ->add('email', EmailType::class, [
                'label' => 'user.email',
                'translation_domain' => 'entity'
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $sent = $this->emailUserVerificator->sendResetMail($email);
            if ($sent) {
                $this->addFlash('success', $translator->trans('email_sent_password_ok'));
            } else {
                $this->addFlash('error', $translator->trans('email_sent_ko'));
            }
            return $this->redirectToSelf(Response::HTTP_SEE_OTHER);
        }

        return $this->renderTemplate([
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/reset_password", name="reset_password")
     */
    public function resetPassword(Request $request, TranslatorInterface $translator): Response
    {

        $currentUri = $request->getUri();

        if ($this->emailUserVerificator->checkResetLinkSignature($currentUri)) {
            $dateUri = $request->get('date', '');
            if ($dateUri == date('Y-m-d')) {
                $email = $request->get('mail', '');
                $stringSanitizer = new StringSanitizer();
                $email = $stringSanitizer->get($email, StringSanitizer::FILTER_MAIL);

                $serverToken = $this->emailUserVerificator->generateEmailToken($email);
                $clientToken = $request->get('token', '');
                if ($serverToken == $clientToken) {
                    /** @var UserRepository $userRepository */
                    $userRepository = $this->getRepository(User::class);
                    $userByMail = $userRepository->findBy(['email' => $email]);
                    $userByMail = reset($userByMail);

                    $form = $this->createForm(RepeatPasswordFormType::class);
                    $form->handleRequest($request);
                    if ($form->isSubmitted() && $form->isValid()) {
                        $userByMail->setPassword(
                            $this->userPasswordHasher->hashPassword(
                                $userByMail,
                                $form->get('plainPassword')->getData()
                            )
                        );
                        $this->getEntityRegistry()->saveObject($userByMail);

                        $this->addFlash('success', $translator->trans('user.reset_password_ok'));
                        return $this->redirectToRoute('user_login', [], Response::HTTP_SEE_OTHER);

                    }

                    return $this->renderTemplate([
                        'form' => $form->createView(),
                    ]);
                }
            }
        }

        $this->addFlash('error', $translator->trans('user.reset_password_ko'));
        return $this->renderTemplate([
        ]);
    }


    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $form = $this->createForm(LoginFormType::class, ['email' => $lastUsername]);
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


    /**
     * @RestrictToRole("user")
     * @Route("/my_account", name="my_account")
     */
    public function myAccount(Request $request, TranslatorInterface $translator)
    {
        $changePwdForm = $this->createForm(ChangePasswordFormType::class);
        $changePwdForm->handleRequest($request);

        if ($changePwdForm->isSubmitted() && $changePwdForm->isValid()) {
            $currentUser = $this->getUser();
            $isCurrentValid = $this->userPasswordHasher->isPasswordValid(
                $currentUser,
                $changePwdForm->get('currentPassword')->getData()
            );
            if ($isCurrentValid) {
                $currentUser->setPassword(
                    $this->userPasswordHasher->hashPassword(
                        $currentUser,
                        $changePwdForm->get('newPassword')->get('plainPassword')->getData()
                    )
                );

                $this->getEntityRegistry()->saveObject($currentUser);
                $this->addFlash('success', $translator->trans('user.password_changed'));
            } else {
                $this->addFlash('error', $translator->trans('user.invalid_password'));
            }
            return $this->redirectToSelf(Response::HTTP_SEE_OTHER);
        }

        return $this->renderTemplate([
            'changePwdForm' => $changePwdForm->createView(),
        ]);
    }



}
