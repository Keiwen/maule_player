<?php

namespace App\Controller;

use App\Entity\User;
use Keiwen\Cacofony\Controller\AppController;

class AbstractAppController extends AppController
{
    /**
     * @return User
     */
    protected function getUser()
    {
        return parent::getUser();
    }


}
