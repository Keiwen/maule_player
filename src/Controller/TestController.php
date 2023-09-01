<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller
 * @Route("/test", name="test_")
 */
class TestController extends AbstractAppController
{


    /**
     * @Route("/theme", name="theme")
     */
    public function testTheme(): Response
    {

        return $this->renderTemplate();
    }



}
