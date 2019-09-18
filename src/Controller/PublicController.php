<?php
// src/Controller/PublicController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class PublicController
 *
 * Deals wth publicly accessible pages.
 *
 * @package App\Controller
 */
class PublicController extends AbstractController
{
    /**
     * Create a new user.
     *
     * @Route("/", name="home")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function home(Request $request) : Response
    {
        return $this->render('home.html.twig');
    }

    /**
     * Register a new user.
     *
     * @Route("/register", name="register")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     */
    public function registerUser(Request $request, UserPasswordEncoderInterface $passwordEncoder) : Response
    {
        // Will return an active user with ROLE_APPLICANT.
        $user = new User();
        $user->setRoles(['ROLE_APPLICANT']);
        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class)
            ->add('username', TextType::class)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            ->add('save', SubmitType::class, array('label' => 'Register'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_user');
        }

        return $this->render('user.html.twig', ['form' => $form->createView()]);
    }
}
