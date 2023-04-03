<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Faite pour que l'autocompletion fonction $this->getUser->getEmail()
 * Si non getEmail n'est pas proposé
 *
 * @method User getUser()
 */
class BaseController extends AbstractController
{

}