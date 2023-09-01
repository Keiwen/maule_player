<?php

namespace App\Controller;

use App\Controller\Mail\MailSender;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Keiwen\Cacofony\Security\Annotation\RestrictToRole;

/**
 * @package App\Controller
 * @Route("/test", name="test_")
 * @RestrictToRole("admin")
 */
class TestController extends AbstractAppController
{

    /**
     * @Route("/email", name="mail")
     */
    public function testMail(MailSender $mailer): Response
    {

        $sent = $mailer->sendMail('romainwald@hotmail.com', 'App Skeleton draft mail', 'test', ['templateVar' => 'variable']);
        if ($sent) {
            $this->addFlash('success', 'Debugging: email sent');
        } else {
            $e = $mailer->getLastException();
            dump($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->renderTemplate();


    }


    /**
     * @Route("/theme", name="theme")
     */
    public function testTheme(): Response
    {

        return $this->renderTemplate();
    }



}
