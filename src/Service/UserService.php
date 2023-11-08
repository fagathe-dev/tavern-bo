<?php
namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserService
{

    private Session $session;

    public function __construct(
        private UserRepository $repository,
        private PaginatorInterface $paginator,
        private UserPasswordHasherInterface $hasher,
        private LoggerInterface $logger,
        private EntityManagerInterface $manager
    ) {
        $this->session = new Session;
    }

    /**
     * update
     *
     * @param  mixed $user
     * @return bool
     */
    public function update(User $user): bool
    {
        $user->setUpdatedAt(new \DateTimeImmutable);

        return $this->save($user);
    }

    /**
     * create
     *
     * @param  mixed $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $user->setCreatedAt(new \DateTimeImmutable);

        return $this->save($user);
    }

    /**
     * save
     *
     * @param  User $user
     * @return bool
     */
    public function save(User $user): bool
    {
        try {
            $this->manager->persist($user);
            $this->manager->flush();
            return true;
        } catch (ORMException $e) {
            $this->session->getFlashBag()->add('danger', 'Une erreur est survenue lors de l\'enregistrement de votre compte !');
            return false;
        }
    }

    /**
     * remove
     *
     * @param  User $object
     * @return object
     */
    public function remove(User $user): bool
    {
        try {
            $this->manager->remove($user);
            $this->manager->flush();
            return true;
        } catch (ORMException $e) {
            $this->session->getFlashBag()->add('danger', 'Une erreur est survenue lors de la suppression de votre compte !');
            return false;
        }
    }

}