<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Emprunt;
use App\Entity\Suggestion;
use App\Entity\Event;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/profil", name="admin")
     * @isGranted("ROLE_ADMIN")
     */
    public function adminDashboard()
    {
        // $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
        // or add an optional message - seen by developers
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Accès non autorisé');

      

        // Voir chemin de redirection à changer si besoin
        return $this->render('admin/index.html.twig');
    }

    
    /**
     * @Route("/admin/profil", name="admin")
     */
    public function profile_index(PaginatorInterface $paginator, Request $request) 
    {

        // Tableau des emprunts

        $donneesEmprunt = $this->getDoctrine()->getRepository(Emprunt::class)->findAll();

        $emprunt = $paginator->paginate(
            $donneesEmprunt, $request->query->getInt('page', 1), 5
        );

        // Tableau des actualités

        $donneesEvent = $this->getDoctrine()->getRepository(Event::class)->findAll();

        $event = $paginator->paginate(
            $donneesEvent, $request->query->getInt('page', 1), 5
        );

        // Tableau des suggestions d'achat

        $donneesSuggestion = $this->getDoctrine()->getRepository(Suggestion::class)->findAll();

        $suggestion = $paginator->paginate(
            $donneesSuggestion, $request->query->getInt('page', 1), 5
        );
        
        return $this->render('admin/index.html.twig', [
            'emprunts' => $emprunt,
            'events' => $event,
            'suggestions' => $suggestion
        ]);
    }


    // edition profil personnel
    
    /**
     * @Route("/admin/{id}/edit", name="admin_edit", methods={"GET","POST"})
     * 
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Profil mis à jour');
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/editprofile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    // Gestion utilisateurs

    // Index

    /**
     * @Route("/admin/users", name="users_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function admin_users_index(UserRepository $userRepository): Response
    {
        return $this->render('admin/users/index.html.twig', [
            'users' => $userRepository->findAll()
        ]);
    }

    /**
     * @Route("/admin/users/new", name="users_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new_user(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Encode password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('users_index');
        }

        return $this->render('admin/users/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }



    // // Edit d'un utilisateur

    /**
     * @Route("admin/users/{id}/edit", name="users_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function admin_users_edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('message', 'Profil mis à jour');
            return $this->redirectToRoute('users_index');
        }

        return $this->render('admin/users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    // Suppression d'un utilisateur

    /**
     * @Route("/admin/users/{id}", name="user_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function admin_users_delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('users_index');
    }


   

}


