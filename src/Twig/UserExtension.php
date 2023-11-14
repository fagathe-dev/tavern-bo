<?php
namespace App\Twig;

use App\Entity\User;
use App\Enum\User\RoleEnum;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction("roles", [$this, "getRoles"]),
        ];
    }

    public function getRoles(User $user): string
    {
        return RoleEnum::getRoles($user);
    }
}