<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class authhandle
{
    public function __invoke(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();
        $data = $event->getData();

        if (!$user instanceof UserInterface) {
            return;
        }

        $roles = $user->getRoles();
        $data['role'] = strtolower($roles[0]); // Adjust as needed
        $event->setData($data);
    }
}
