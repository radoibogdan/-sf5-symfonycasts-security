<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseController
{
    /**
     * @Route("/api/me")
     */
    public function apiMe()
    {
        // Create JSON Response class
        // Install composer req serializer avant sinon on aura un {} vide => Symfony prend en compte direct le serializer
        // Sans serializer php php utilise les propriétés public de l'objet dans le php_encode
        return $this->json($this->getUser(), 200, [], [
            'groups' => ['user:read'] # dans User entity on choisit les champs a serializer
        ]);
    }
}