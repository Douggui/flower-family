<?php

namespace App\Repository;

use App\Entity\Option;
use App\Entity\Product;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Option>
 *
 * @method Option|null find($id, $lockMode = null, $lockVersion = null)
 * @method Option|null findOneBy(array $criteria, array $orderBy = null)
 * @method Option[]    findAll()
 * @method Option[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Option::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Option $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Option $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Option[] Returns an array of Option objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    public function getProductOptions(Product $product)
    {
        return $this->createQueryBuilder('o')
                    ->select('o')
                    ->join('o.products','p')
                    ->where('p.id = :id')
                    ->setParameter('id',$product->getId())
                    ->join('o.stocks','s')
                    ->addSelect('s.stock')
                    ->andWhere('s.product = :id')
                    ->setParameter('id',$product->getId())
                    ->getQuery()
                    ->getResult()
                    ;
    }
     public function getProductOption(Product $product , string $option)
     {
        return $this->createQueryBuilder('o')
                    ->join('o.products','p')
                    ->where('p.id = :id')
                    ->setParameter('id',$product->getId())
                    ->andWhere('o.name = :name')
                    ->setParameter('name' , $option)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
     }
    /*
    public function findOneBySomeField($value): ?Option
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}