<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Emprunt;
use App\Form\EmpruntType;
use App\Repository\EmpruntRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("/emprunt")
 */
class EmpruntController extends AbstractController
{
    /**
     * @Route("/", name="emprunt_index", methods={"GET"})
     */
    public function index(EmpruntRepository $empruntRepository): Response
    {
        return $this->render('emprunt/index.html.twig', [
            'emprunts' => $empruntRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{bookId}", name="emprunt_new", methods={"GET","POST"})
     * @isGranted("ROLE_USER")
     */
    public function new(Request $request, string $bookId): Response
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($bookId);
        if ($book==null){
            throw new BadRequestHttpException('Aucun emprunt trouvé');
        }
        $user = $this->getUser();
        $emprunt = new Emprunt();
        $form = $this->createForm(EmpruntType::class, $emprunt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emprunt->setBook($book);
            $emprunt->setUser($user);
            $emprunt->setDateDebut(new \DateTime('now'));
            $emprunt->setDateFin(new \DateTime('+30 days'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($emprunt);
            $entityManager->flush();
            $this->addFlash('message', 'Votre demande d\'emprunt a été demandée. Elle sera examinée par nos bibliothècaires');

            return $this->redirectToRoute('profil_index');
        }

        return $this->render('emprunt/new.html.twig', [
            'emprunt' => $emprunt,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="emprunt_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Emprunt $emprunt): Response
    {
        if ($this->isCsrfTokenValid('delete'.$emprunt->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($emprunt);
            $entityManager->flush();
        }

        return $this->redirectToRoute('emprunt_index');
    }

    // Validation de l'emprunt

    /**
     * @Route("/{id}/validation", name="emprunt_validation", methods={"GET"})
     */
    public function validation(Emprunt $emprunt)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $newValidation = !
        $emprunt->getValidation();
        $emprunt->setValidation($newValidation);
        $entityManager->persist($emprunt);
        $entityManager->flush();
        $this->addFlash('message', 'Validation de l\'emprunt effectuée');
        return $this->redirectToRoute('librarian_profile');
    }

}
