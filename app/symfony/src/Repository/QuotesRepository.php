<?php

namespace App\Repository;

use App\Entity\Quotes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quotes>
 *
 * @method Quotes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quotes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quotes[]    findAll()
 * @method Quotes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuotesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quotes::class);
    }
    public function get(string $currency) {
        $em = $this->getEntityManager();
        $quote = $em->getRepository(Quotes::class)->findOneBy(["currency" => $currency]);
        return $quote;
    }
    public function add(Quotes $quote): void {
        $em = $this->getEntityManager();
        $em->persist($quote);
        $em->flush();
    }

    public function remove(string $currency): void  {
        $em = $this->getEntityManager();
        $quote = $em->getRepository(Quotes::class)->findOneBy(["currency"=> $currency]);
        $em->remove($quote);
        $em->flush();
    }

    public function update(string $currency, float $rate): void {
        $em = $this->getEntityManager();
        
        $quote = $em->getRepository(Quotes::class)->findOneBy(["currency" => $currency]);
        $quote->setCurrency($currency);
        $quote->setRate($rate);

        $em->persist($quote);
        $em->flush();
    }

    
    /**
        *Возвращает массив из котировок: [
        *    [ "currency" => "EUR", "rate" => 1], ....
        *]
    */
    public function get_all(): array {


        
        $em = $this->getEntityManager();
        $quotes = $em->getRepository(Quotes::class)->findAll();


        $result = [];
        foreach ($quotes as $quote) {
            $result[] = [
                "currency" => $quote->getCurrency(),
                "rate"=> $quote->getRate()
            ];
        }


        return $result;
    }

//    /**
//     * @return Quotes[] Returns an array of Quotes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('q.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Quotes
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
