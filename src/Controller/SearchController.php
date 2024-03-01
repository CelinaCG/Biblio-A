<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\AdvancedSearchType;
use App\Form\SearchType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


class SearchController extends AbstractController
{
    // Recherche simple

    /**
     * @Route("/recherche-simple", name="recherche_simple")
     */
    public function rechercheSimple(Request $request, BookRepository $repo, PaginatorInterface $paginator)
    {

        $searchForm = $this->createForm(SearchType::class);

        $searchForm->handleRequest($request);

        $donnees = $repo->findAll();

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $moteur = $searchForm->get('searchEngine')->getData();
            $donnees = $repo->rechercheSimple($moteur);

            if ($donnees == null) {
                $this->addFlash('warning', 'Aucune référence n\'a été trouvée. Merci de bien vouloir réessayer avec différents mots clés.');
            }
        }

        // Pagination des résultats
        $book = $paginator->paginate(
            // Doctrine Query, not results
            $donnees,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            4
        );

        return $this->render('search/rechercheSimple.html.twig', [
            'books' => $book,
            'searchForm' => $searchForm->createView()
        ]);
    }

    // Recherche avancée

    /**
     * @Route("/recherche-avancee", name="recherche_avancee")
     */
    public function rechercheAvancee(Request $request, BookRepository $bookRepository, PaginatorInterface $paginator){

        $searchForm = $this->createForm(AdvancedSearchType::class);
        $searchForm->handleRequest($request);

        $donnees = $bookRepository->findAll();

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {

            $titre = $searchForm->getData()->getTitre();
            $auteur = $searchForm->getData()->getAuteur();
            $coauteur = $searchForm->getData()->getCoauteur();
            $editeur = $searchForm->getData()->getEditeur();
            $annee = $searchForm->getData()->getAnnee();
            $langue = $searchForm->getData()->getLangue();
            $typeDocument = $searchForm->getData()->getTypeDocument();
            $categorie = $searchForm->getData()->getCategorie();

            $donnees = $bookRepository->findByRechercheAvancee($titre, $auteur, $coauteur, $editeur, $annee, $langue, $typeDocument, $categorie);

            if ($donnees == null) {
                $this->addFlash('warning', 'Aucune référence n\'a été trouvée. Merci de bien vouloir réessayer avec différents mots clés.');
            }
        }

        // Paginate the results of the query
        $book = $paginator->paginate(
            // Doctrine Query, not results
            $donnees,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            4
        );

        return $this->render('search/rechercheAvancee.html.twig', [
            'books' => $book,
            'searchForm' => $searchForm->createView()
        ]);

    }



}
