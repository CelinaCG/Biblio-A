<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("/reservation")
 */
class ReservationController extends AbstractController
{
    

    // Nouvelle réservation

    /**
     * @Route("/nouveau/{bookId}", name="reservation_new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request, string $bookId): Response
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($bookId);
        if ($book==null){
            throw new BadRequestHttpException('Aucune réservation trouvée');
        }

        $user = $this->getUser();
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setBook($book);
            $reservation->setUser($user);
            $reservation->setDateDebut(new \DateTime('now'));
            $reservation->setDateFin(new \DateTime('+14 days'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();
            $this->addFlash('message', 'Votre demande de réservation a été demandée.Elle sera étudiée par nos soins');

            return $this->redirectToRoute('profil_index');
        }

        return $this->render('reservation/new.html.twig', [
            'reservations' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservation_show", methods={"GET"})
     */
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservations' => $reservation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="reservation_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Reservation $reservation): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reservation_index');
        }

        return $this->render('reservation/edit.html.twig', [
            'reservations' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservation_delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request, Reservation $reservation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reservation);
            $entityManager->flush();
            $this->addFlash('success', 'Entrée supprimée avec succès');
        }

        return $this->redirectToRoute('reservation_index');
    }

}
