<?php


namespace App\Twig;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class UserDataExtension extends AbstractExtension implements GlobalsInterface
{
    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function getGlobals(): array
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        if (!$user) {
            return [
                'user_data' => [
                    'role' => null,
                    'id' => null,
                    'getImageprofil' => null,
                ],
            ];
        }

        $role = $user->getRoles()[0] ?? null;
        $role = $role ? strtolower(str_replace('ROLE_', '', $role)) : null;
        $id = $user->getId();
        $profilePic = $user->getImageprofil();

        return [
            'user_data' => [
                'role' => $role,
                'id' => $id,
                'getImageprofil' => $profilePic,
            ],
        ];
    }
}
