<?php

namespace App\Security\Voter;

use App\Entity\Question;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class QuestionVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // return false => stop process, voter abstains
        return in_array($attribute, ['EDIT'])
            && $subject instanceof \App\Entity\Question;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Si $subject n'est pas une question renvoyer une exception + Aide PhpStorm pour typer le $subject
        if (!$subject instanceof Question) {
            throw new \Exception("Le subject n'est pas de type Question");
        }

        # si ROLE_ADMIN autoriser l'accès
//        dump($this->security->isGranted('ROLE_ADMIN'));
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'EDIT':
                return $user === $subject->getOwner();
        }

        return false;
    }
}
