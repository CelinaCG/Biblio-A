<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    // Recherche simple

    public function rechercheSimple($searchEngine){
        return $this->createQueryBuilder('Book')
            ->andWhere('Book.titre LIKE :searchEngine')
            ->orWhere('Book.auteur LIKE :searchEngine')
            ->orWhere('Book.editeur LIKE :searchEngine')
            ->orWhere('Book.annee = :searchEngine')
            ->orWhere('Book.coauteur LIKE :searchEngine')
            ->orWhere('Book.langue LIKE :searchEngine')
            ->orWhere('Book.typeDocument LIKE :searchEngine')
            ->orWhere('Book.categorie LIKE :searchEngine')
            ->setParameter('searchEngine', '%'.$searchEngine.'%')
            ->getQuery()
            ->execute()
        ;
    }

    // Recherche avancée

    public function findByRechercheAvancee($titre, $auteur, $editeur, $annee, $coauteur, $langue, $typeDocument, $categorie)
    {
        $queryBuilder = $this->createQueryBuilder('Book');
            if(empty($titre) === false){
                $queryBuilder->andWhere('Book.titre LIKE :titre')
                ->setParameter('titre', '%'.$titre.'%');
            }

            if(empty($auteur) === false){
                $queryBuilder->andWhere('Book.auteur LIKE :auteur')
                ->setParameter('auteur', '%'.$auteur.'%');
            }

            if(empty($editeur) === false){
                $queryBuilder->andWhere('Book.editeur LIKE :editeur')
                ->setParameter('editeur', '%'.$editeur.'%');
            }

            if(empty($annee) === false){
                $queryBuilder->andWhere('Book.annee = :annee')
                ->setParameter('annee', '%'.$annee.'%');
            }

            if(empty($coauteur) === false){
                $queryBuilder->andWhere('Book.coauteur LIKE :coauteur')
                ->setParameter('coauteur', '%'.$coauteur.'%');
            }

            if(empty($langue) === false){
                $queryBuilder->andWhere('Book.langue LIKE :langue')
                ->setParameter('langue', '%'.$langue.'%');
            }

            if(empty($typeDocument) === false){
                $queryBuilder->andWhere('Book.typeDocument LIKE :typeDocument')
                ->setParameter('typeDocument', '%'.$typeDocument.'%');
            }

            if(empty($categorie) === false){
                $queryBuilder->andWhere('Book.categorie LIKE :categorie')
                ->setParameter('categorie', '%'.$categorie.'%');
            }
            
        ;

        return $queryBuilder->getQuery()->execute();

    }

   

    // /**
    //  * @return Book[] Returns an array of Book objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
