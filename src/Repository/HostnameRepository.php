<?php

namespace App\Repository;

use App\Entity\Hostname;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Hostname|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hostname|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hostname[]    findAll()
 * @method Hostname[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HostnameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hostname::class);
    }

    // /**
    //  * @return Hostname[] Returns an array of Hostname objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Hostname
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
