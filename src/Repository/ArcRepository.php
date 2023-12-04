<?php

namespace App\Repository;

use App\Entity\Arc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Arc>
 *
 * @method Arc|null find($id, $lockMode = null, $lockVersion = null)
 * @method Arc|null findOneBy(array $criteria, array $orderBy = null)
 * @method Arc[]    findAll()
 * @method Arc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArcRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Arc::class);
    }

    /**
     * findLastPosition
     *
     * @return Arc
     */
    public function findLastPosition(): Arc {
        return $this->createQueryBuilder('a')
            ->orderBy('a.position', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * findArcAfter
     *
     * @param  int $position
     * @return array Arc[]
     */
    public function findArcAfter(int $position): array {
        return $this->createQueryBuilder('a')
            ->andWhere('a.position = :val')
            ->setParameter('val', $position)
            ->orderBy('a.position', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
//     * @return Arc[] Returns an array of Arc objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    //    public function findOneBySomeField($value): ?Arc
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
