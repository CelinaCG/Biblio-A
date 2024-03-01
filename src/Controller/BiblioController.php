<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Event;
use App\Repository\BookRepository;
use App\Repository\EventRepository;
use App\Repository\ReservationRepository;
use Exception;
use Gedmo\Mapping\Annotation\Slug;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BiblioController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index(): Response
    {
        $event = $this->getDoctrine()->getRepository(Event::class)->findBy([], ['createdAt' => 'desc'], $limit = 2, null);

        $book = $this->getDoctrine()->getRepository(Book::class)->findBy([], ['createdAt' => 'desc'], $limit = 4, null);

    

        return $this->render('biblio/index.html.twig', [
            "events" => $event,
            "books" => $book,
        ]);
    }

    //  Page actualité

    /**
     * @Route("/actualite", name="actualite")
     */
    public function actualite(EventRepository $eventRepository): Response 
    {

        
        return $this->render("biblio/actualite.html.twig", [
            'events' => $eventRepository->findAll(),
        ]);
    }

    // Actualité en détail

    /**
     * @Route("/actualite/{slug}", name="actualite_detail", methods={"GET"})
     */
    public function actualiteDetail(Event $event, EventRepository $eventRepository, string $slug): Response 
    {
        $event = $eventRepository->findOneBy(['slug' => $slug]);
        
        return $this->render("biblio/actualite_detail.html.twig", [
            'events' => $event,
        ]);
    }

    // ----------- début menu dropdown infos pratiques ------------ 

    // Horaires

    /**
     * @Route("/horaires", name="horaires")
     */
    public function horaires() 
    {
        return $this->render("biblio/horaires.html.twig", []);
    }

    // Inscription

    /**
     * @Route("/conditions-inscription", name="inscription")
     */
    public function inscription() 
    {
        return $this->render("biblio/inscription.html.twig", []);
    }


    // Présentation et accès bibliothèque

    /**
     * @Route("/presentation", name="presentation")
     */
    public function presentation() 
    {
        return $this->render("biblio/presentation.html.twig", []);
    }

    // Suggestion d'achat

    /**
     * @Route("/suggestion", name="suggestion")
     */
    public function suggestion() 
    {
        return $this->render("biblio/suggestion.html.twig", []);
    }


    // --------------- fin menu dropdown infos pratiques ---------------

    // Détail d'un livre

    /**
     * @Route("/detail/{slug}", name="detail_book", methods={"GET"})
     */
    public function detailBook(Book $book, ReservationRepository $reservationRepository, BookRepository $bookRepository, string $slug): Response
    {
        $reservation = $book->getReservations();
        $emprunt = $book->getEmprunts();
        $book = $bookRepository->findOneBy(['slug' => $slug]);

        if (!$book) {
            throw new Exception("Il n\'a pas de livre associé à cet ID.");
        }

        return $this->render("biblio/detail_book.html.twig", [
            "book" => $book,
            'reservations' => $reservation,
            'emprunts' => $emprunt
           
        ]);
    }

    // Selections des bibliothècaires

    // Selection jeunesse

    /**
     * @Route("/selection/jeunesse", name="selectionjeunesse")
     */
    public function selectionBdJeunesse(Request $request, PaginatorInterface $paginator): Response
    {
        // BD/Manga
        $selectionBdJeunesse = $this->getDoctrine()->getRepository(Book::class)->findBy(array("typeDocument" => "Bande dessinée jeunesse", "favori" => true ));

        $selectionBdJeunesse = $paginator->paginate($selectionBdJeunesse, $request->query->getInt('page', 1), 4
        );

        // Livre

        $selectionLivreJeunesse = $this->getDoctrine()->getRepository(Book::class)->findBy(array("typeDocument" => "Livre jeunesse", "favori" => true ));

        $selectionLivreJeunesse = $paginator->paginate($selectionLivreJeunesse, $request->query->getInt('page', 1), 4
        );

        // Livre scolaire

        $selectionLivreScolaire = $this->getDoctrine()->getRepository(Book::class)->findBy(array("typeDocument" => "Livre scolaire", "favori" => true));

        $selectionLivreScolaire = $paginator->paginate($selectionLivreScolaire, $request->query->getInt('page', 1), 4);

        return $this->render('biblio/selection/selectionjeunesse.html.twig', [
            "selectionBdJeunesse" => $selectionBdJeunesse,
            "selectionLivreJeunesse" => $selectionLivreJeunesse,
            "selectionLivreScolaire" => $selectionLivreScolaire

        ]);
    }

   
    // Selection livre adulte

    /**
     * @Route("/selection/adulte", name="selectionadulte")
     */
    public function selectionLivreAdulte(Request $request, PaginatorInterface $paginator): Response
    {
        // Livres

        $selectionLivreAdulte = $this->getDoctrine()->getRepository(Book::class)->findBy(array("typeDocument" => "Livre adulte", "favori" => true ));

        $selectionLivreAdulte = $paginator->paginate($selectionLivreAdulte, $request->query->getInt('page', 1), 4
        );

        // BD/Comics/Manga

        $selectionBdAdulte = $this->getDoctrine()->getRepository(Book::class)->findBy(array("typeDocument" => "Bande dessinée adulte", "favori" => true ));

        $selectionBdAdulte = $paginator->paginate($selectionBdAdulte, $request->query->getInt('page', 1), 4
        );

        // Documentaire/Beau-livre

        $selectionDocAdulte = $this->getDoctrine()->getRepository(Book::class)->findBy(array("typeDocument" => "Documentaire", "favori" => true));

        $selectionDocAdulte = $paginator->paginate($selectionDocAdulte, $request->query->getInt('page', 1), 4);

        // Emploi/Formation

        $selectionFormationAdulte = $this->getDoctrine()->getRepository(Book::class)->findBy(array("typeDocument" => "Emploi/Formation", "favori" => true));

        $selectionFormationAdulte = $paginator->paginate($selectionFormationAdulte, $request->query->getInt('page', 1), 4);
       
        return $this->render('biblio/selection/selectionadulte.html.twig', [
            "selectionLivreAdulte" => $selectionLivreAdulte,
            "selectionBdAdulte" => $selectionBdAdulte,
            "selectionDocAdulte" => $selectionDocAdulte,
            "selectionFormationAdulte" => $selectionFormationAdulte
          
        ]);
    }

    // Nouveautés

    /**
     * @Route("/selection/nouveautes", name="nouveautes")
     */
    public function nouveautes(Request $request, PaginatorInterface $paginator): Response
    {
        // Adultes

        // Livres 

        $nouveauteLivreAdulte = $this->getDoctrine()->getRepository(Book::class)->findBy(array("typeDocument" => "Livre adulte"), array("createdAt" => "desc"));

        $nouveauteLivreAdulte = $paginator->paginate($nouveauteLivreAdulte, $request->query->getInt('page', 1), 4);

        // BD/Comics/Manga

        $nouveauteBdAdulte = $this->getDoctrine()->getRepository(Book::class)->findBy(array("typeDocument" => "Bande dessinée adulte"), array("createdAt" => "desc"));

        $nouveauteBdAdulte = $paginator->paginate($nouveauteBdAdulte, $request->query->getInt('page', 1), 4);

        // Documentaire/Beau-livre

        $nouveauteDocAdulte = $this->getDoctrine()->getRepository(Book::class)->findBy(array("typeDocument" => "Documentaire"), array("createdAt" => "desc"));

        $nouveauteDocAdulte = $paginator->paginate($nouveauteDocAdulte, $request->query->getInt('page', 1), 4);

        // Emploi/Formation

        $nouveauteFormationAdulte = $this->getDoctrine()->getRepository(Book::class)->findBy(array("typeDocument" => "Emploi/Formation"), array("createdAt" => "desc"));

        $nouveauteFormationAdulte = $paginator->paginate($nouveauteFormationAdulte, $request->query->getInt('page', 1), 4);

        // Jeunesse

        // Livres

        $nouveauteLivreJeunesse = $this->getDoctrine()->getRepository(Book::class)->findBy(array("typeDocument" => "Livre jeunesse"), array("createdAt" => "desc"));

        $nouveauteLivreJeunesse = $paginator->paginate($nouveauteLivreJeunesse, $request->query->getInt('page', 1), 4);

        // BD/Comics/Manga

        $nouveauteBdJeunesse = $this->getDoctrine()->getRepository(Book::class)->findBy(array("typeDocument" => "Bande dessinée jeunesse"), array("createdAt" => "desc"));

        $nouveauteBdJeunesse = $paginator->paginate($nouveauteBdJeunesse, $request->query->getInt('page', 1), 4);

        // Livre scolaire

        $nouveauteDocJeunesse = $this->getDoctrine()->getRepository(Book::class)->findBy(array("typeDocument" => "Livre scolaire"), array("createdAt" => "desc"));

        $nouveauteDocJeunesse = $paginator->paginate($nouveauteDocJeunesse, $request->query->getInt('page', 1), 4);

        // Livres étrangers

        $nouveauteLivreEtranger = $this->getDoctrine()->getRepository(Book::class)->findBy(array("typeDocument" => "Livres étrangers"), array("createdAt" => "desc"));

        $nouveauteLivreEtranger = $paginator->paginate($nouveauteLivreEtranger, $request->query->getInt('page', 1), 4);


       
        return $this->render('biblio/selection/nouveautes.html.twig', [
            "nouveauteLivreAdulte" => $nouveauteLivreAdulte,
            "nouveauteBdAdulte" => $nouveauteBdAdulte,
            "nouveauteDocAdulte" => $nouveauteDocAdulte,
            "nouveauteFormationAdulte" => $nouveauteFormationAdulte,            
            "nouveauteLivreJeunesse" => $nouveauteLivreJeunesse,
            "nouveauteBdJeunesse" => $nouveauteBdJeunesse,
            "nouveauteDocJeunesse" => $nouveauteDocJeunesse,
            "nouveauteLivreEtranger" => $nouveauteLivreEtranger
          
        ]);
    }

    // Autoformation

    /**
     * @Route("/autoformation", name="autoformation")
     */
    public function AutoFormation() {
        return $this->render("biblio/autoformation.html.twig", []);
    }

   

    // CGU

    /**
     * @Route("/conditions-utilisation", name="cgu")
     */
    public function CGU() {
        return $this->render("biblio/cgu.html.twig", []);
    }

    // Mentions légales

    /**
     * @Route("/mentions-legales", name="mentions_legales")
     */
    public function mentionsLegales() {
        return $this->render("biblio/mentionslegales.html.twig", []);
    }

    // Politique de confidentialit&

    /**
     * @Route("/politique-confidentialite", name="politique_confidentialite")
     */
    public function confidentialite() 
    {
        return $this->render("biblio/politiqueconfidentialite.html.twig", []);
    }



}
