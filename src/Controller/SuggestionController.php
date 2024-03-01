<?php

namespace App\Controller;

use App\Entity\Suggestion;
use App\Form\SuggestionType;
use App\Repository\SuggestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/suggestion")
 */
class SuggestionController extends AbstractController
{
    /**
     * @Route("/nouveau", name="suggestion_new", methods={"GET","POST"})
     * @isGranted("ROLE_USER")
     */
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        $suggestion = new Suggestion();
        $form = $this->createForm(SuggestionType::class, $suggestion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $suggestion->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($suggestion);
            $entityManager->flush();
            $this->addFlash('message', 'Votre demande a bien été envoyé.');

            return $this->redirectToRoute('profil_index');
        }

        return $this->render('suggestion/new.html.twig', [
            'suggestion' => $suggestion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="suggestion_delete", methods={"DELETE"})
     * @isGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Suggestion $suggestion): Response
    {
        if ($this->isCsrfTokenValid('delete'.$suggestion->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($suggestion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/{id}/validation", name="suggestion_validation", methods={"GET"})
     * @isGranted("ROLE_ADMIN")
     */
    public function validation(Suggestion $suggestion)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $newValidation = !
        $suggestion->getValidation();
        $suggestion->setValidation($newValidation);
        $entityManager->persist($suggestion);
        $entityManager->flush();
       
        return $this->redirectToRoute('admin');
    }
}
