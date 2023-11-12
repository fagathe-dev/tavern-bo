<?php
namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\ServiceTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserService
{

    use ServiceTrait;

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
     * hash
     *
     * @param  mixed $user
     * @return User
     */
    private function hash(User $user): User
    {
        return $user->setPassword(
            $this->hasher->hashPassword($user, $user->getPassword())
        );
    }

    /**
     * create
     *
     * @param  mixed $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $user->setCreatedAt(new \DateTimeImmutable)
            ->setConfirm(true);
        $this->hash($user);

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
    public function remove(User $user): bool|object
    {
        try {
            $this->manager->remove($user);
            $this->manager->flush();
            return $this->sendNoContent();
        } catch (ORMException $e) {
            $this->session->getFlashBag()->add('danger', 'Une erreur est survenue lors de la suppression de votre compte !');
            return false;
        }
    }

    /**
     * @param  mixed $request
     * @return PaginationInterface
     */
    public function getUsers(Request $request): PaginationInterface
    {

        $data = $this->repository->findAll(); #findUsersAdmin();
        $page = $request->query->getInt('page', 1);
        $nbItems = $request->query->getInt('nbItems', 15);

        return $this->paginator->paginate(
            $data,
            /* query NOT result */
            $page,
            /*page number*/
            $nbItems, /*limit per page*/
        );
    }

    public function index(Request $request): array
    {
        $paginatedUsers = $this->getUsers($request);

        return compact('paginatedUsers');
    }

}