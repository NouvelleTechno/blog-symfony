<?php

namespace App\Repository;

use App\Entity\Articles;
use App\Entity\Categories;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\AST\Functions\ConcatFunction;

/**
 * @method Articles|null find($id, $lockMode = null, $lockVersion = null)
 * @method Articles|null findOneBy(array $criteria, array $orderBy = null)
 * @method Articles[]    findAll()
 * @method Articles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articles::class);
    }

    /**
     * @return Articles[] Returns an array of Articles objects
    */
    public function findAllArray() : array
    {
        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.created_at', 'DESC');


        $query = $qb->getQuery();

        return $query->execute();
    }
    

    /**
     * @return Articles[] Returns an array of Articles objects
    */
    public function apiFindAll() : array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.id', 'a.titre', 'a.contenu', 'a.featured_image', 'a.created_at')
            ->orderBy('a.created_at', 'DESC');

        $query = $qb->getQuery();

        return $query->execute();
    }


    // /**
    //  * @return Articles[] Returns an array of Articles objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Articles
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
