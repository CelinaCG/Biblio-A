<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @isGranted("ROLE_ADMIN")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/biblio/event", name="event_index", methods={"GET"})
     */
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    /**
     * @Route("/biblio/event/nouveau", name="event_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $ImageEventFile */
            $ImageEventFile = $form->get('imageEvent')->getData();
            if ($ImageEventFile){
                $originalFilename = pathinfo($ImageEventFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$ImageEventFile->guessExtension();

                try {
                    $ImageEventFile->move($this->getParameter('event_directory'), 
                    $newFilename
                );
                } catch (FileException $e) {}
                $event->setImageEvent($newFilename);

            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();
            $this->addFlash("success", "Votre entrée a été ajoutée.");

            return $this->redirectToRoute('event_index');
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/biblio/event/{id}", name="event_show", methods={"GET"})
     */
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * @Route("/biblio/event/{id}/edit", name="event_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Event $event): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $ImageEventFile */
            $ImageEventFile = $form->get('imageEvent')->getData();
            if ($ImageEventFile){
                $originalFilename = pathinfo($ImageEventFile->getClientOriginalName(), PATHINFO_FILENAME);
               
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$ImageEventFile->guessExtension();

                try {
                    $ImageEventFile->move(
                        $this->getParameter('event_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {}

                $event->setImageEvent($newFilename);
              
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success", "Votre entrée a été modifiée.");

            return $this->redirectToRoute('event_index');
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/biblio/event/{id}", name="event_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Event $event): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
            $this->addFlash("success", "Votre entrée a été supprimée.");
        }

        return $this->redirectToRoute('event_index');
    }
}
