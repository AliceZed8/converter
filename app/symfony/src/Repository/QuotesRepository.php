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

    public function add_quote(string $currency, float $rate) {
        $em = $this->getEntityManager();

        $quote = new Quotes();
        $quote->setCurrency($currency);
        $quote->setRate($rate);

        $em->persist($quote);
        $em->flush();
        return true;
    }

    public function remove_quote(string $currency) {
        $em = $this->getEntityManager();
    }

    public function update_quote(string $currency, int $rate) {
        $em = $this->getEntityManager();
        
        $quote = $em->getRepository(Quotes::class)->findOneBy(["currency" => $currency]);
        $quote->setCurrency($currency);
        $quote->setRate($rate);

        $em->persist($quote);
        $em->flush();
        return true;
    }


    public function get_all_quotes(): array {
        $em = $this->getEntityManager();
        $quotes = $em->getRepository(Quotes::class)->findAll();
        return $quotes;
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
