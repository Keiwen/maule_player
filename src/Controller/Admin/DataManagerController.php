<?php

namespace App\Controller\Admin;

use App\Entity;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller
 * @Route("/datamanager", name="admin_")
 */
class DataManagerController extends AbstractDashboardController
{

    const ICON_USER = 'fa fa-users';


    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('data_manager/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Data Manager')
            ->setFaviconPath('/img/main_icon_bw.png')
        ;
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),
            MenuItem::section('Entities'),
            MenuItem::linkToCrud('User', self::ICON_USER, Entity\User::class),
            MenuItem::section('-----'),
            MenuItem::linkToRoute('Exit', 'fa fa-door-open', 'index'),
        ];
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }

}
