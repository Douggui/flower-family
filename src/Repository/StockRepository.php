<?php

namespace App\Repository;

use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stock>
 *
 * @method Stock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stock[]    findAll()
 * @method Stock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Stock $entity, bool $flush = true): void
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
    public function remove(Stock $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Stock[] Returns an array of Stock objects
    //  */
    
    public function findBySpecification($value,$product)
    {
        if($value=""){
            return $this->createQueryBuilder('s')
            ->andWhere('s.product=:product')
                ->andWhere('s.productOption = :val')
                ->setParameters(['val'=>null,'product'=>$product])
                ->getQuery()
                //->getResult()
            ;
        }else{
            return $this->createQueryBuilder('s')
            ->andWhere('s.product=:product')
                ->andWhere('s.productOption = :val')
                ->setParameters(['val'=>$value,'product'=>$product])
                ->getQuery()
                //->getResult()
            ;
        }
       
    }
    public function findBySpecifications($value,$product)
    {
        if($value=""){
            $conn = $this->getEntityManager()->getConnection();

            $sql = '
                SELECT * FROM stock s
                WHERE s.product_id =:product AND s.product_option_id IS NULL
                
                ';
            $stmt = $conn->prepare($sql);
            $resultSet = $stmt->executeQuery(['product' => $product]);

        // returns an array of arrays (i.e. a raw data set)
        
        return $resultSet->fetchAllAssociative();
        }else{
            $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM stock s
            WHERE s.product_id=:product AND s.product_option_id=:value
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['product' => $product,'value'=>$product]);
        
        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
        }
       
    }

    
    

    /*
    public function findOneBySomeField($value): ?Stock
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}