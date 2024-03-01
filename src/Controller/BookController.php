<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/book")
 * @isGranted("ROLE_ADMIN")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/", name="book_index", methods={"GET"})
     */
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    /**
     * @Route("/ajout", name="book_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $ImageLivreFile */
            $ImageLivreFile = $form->get('imageLivre')->getData();
            // condition nécéssaire parce que le champ 'imageLivre' n'est pas requis.
            // Donc le fichier doit être mis en place seulement s'il est ajouté
            if ($ImageLivreFile){
                $originalFilename = pathinfo($ImageLivreFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Inclure le nom du fichier comme faisant partie de l'url
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$ImageLivreFile->guessExtension();

                // Déplacer le fichier vers le dossier de stockage
                try {
                    $ImageLivreFile->move(
                        $this->getParameter('bookcover_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {}

                // mise à jour de la propriété 'ImageLivre' dans l'entité book pour stocker le fichier
                $book->setImageLivre($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();
            // message pop-up de confirmation
            $this->addFlash("success", "Votre entrée a été ajoutée.");

            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="book_show", methods={"GET"})
     */
    public function show(Book $book, BookRepository $bookRepository, string $slug): Response
    {
        $book = $bookRepository->findOneBy(['slug' => $slug]);
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="book_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Book $book): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $ImageLivreFile */
            $ImageLivreFile = $form->get('imageLivre')->getData();
            // condition nécéssaire parce que le champ 'imageLivre' n'est pas requis.
            // Donc le fichier doit être mis en place seulement s'il est ajouté
            if ($ImageLivreFile){
                $originalFilename = pathinfo($ImageLivreFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Inclure le nom du fichier comme faisant partie de l'url
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$ImageLivreFile->guessExtension();

                // Déplacer le fichier vers le dossier de stockage
                try {
                    $ImageLivreFile->move(
                        $this->getParameter('bookcover_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {}

                // mise à jour de la propriété 'ImageLivre' dans l'entité book pour stocker le fichier
                $book->setImageLivre($newFilename);
            }


            $this->getDoctrine()->getManager()->flush();
            
            // message pop-up de confirmation
            $this->addFlash("success", "Votre entrée a été modifiée.");

            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="book_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Book $book): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();
            // message pop-up de confirmation
            $this->addFlash("success", "Votre entrée a été supprimée.");
        }

        return $this->redirectToRoute('book_index');
    }


    // Sélection des titres favoris

    /**
     * @Route("/{id}/favori", name="book_favori", methods={"GET"})
     */
    public function favori(Book $book)
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $newFavori = ! 
        $book->getFavori();
        $book->setFavori($newFavori);
        $entityManager->persist($book);
        $entityManager->flush();
        $this->addFlash('success', 'Le titre a bien été mis en favori !');
        

        return $this->redirectToRoute('book_index');
    }


}
