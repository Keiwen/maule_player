<?php

namespace App\Controller;

use Keiwen\Utils\Sanitize\StringSanitizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractAppController
{

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {

        return $this->renderTemplate();
    }

    /**
     * @Route("/theme/change/{theme}", name="theme_change")
     */
    public function themeChange(string $theme): Response
    {
        $theme = (new StringSanitizer())->get($theme, StringSanitizer::FILTER_ALPHA);

        $this->getResponse()->setCookie('app_theme', $theme);
        return $this->redirectToReferer();
    }


}
