<?php

namespace App\Security;

use App\Entity\User;
use App\Enum\User\RoleEnum;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppLoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private UserRepository $userRepository
    ) {
    }

    public function supports(Request $request): bool
    {
        return $request->isMethod('POST') && $this->urlGenerator->generate(self::LOGIN_ROUTE) === $request->getPathInfo();
    }

    private function isAllowedUser(User $user): bool
    {
        $allowedRoles = [RoleEnum::ROLE_MANAGER, RoleEnum::ROLE_ADMIN];

        foreach ($user->getRoles() as $role) {
            if (in_array($role, $allowedRoles)) {
                return true;
            }
        }

        return false;
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('username', '');
        $password = $request->request->get('password', '');
        $rememberMe = $request->request->get('_remember_me', '');

        $user = $this->userRepository->findOneBy(['email' => $username]);

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username);

        $badges = [new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token'))];

        if ($rememberMe && $rememberMe === "on") {
            $badges = [...$badges, (new RememberMeBadge)->enable()];
        }

        if ($user instanceof User) {
            if ($user->isConfirm() === false) {
                throw new CustomUserMessageAuthenticationException('Veuillez confirmer votre compte.');
            }
            if ($this->isAllowedUser($user)) {

                return new Passport(
                    new UserBadge($username),
                    new PasswordCredentials($password),
                    $badges
                );
            }

            throw new CustomUserMessageAuthenticationException('Accès non autorisé.');
        }

        throw new CustomUserMessageAuthenticationException('Identifiants incorrects.');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // For example:
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
