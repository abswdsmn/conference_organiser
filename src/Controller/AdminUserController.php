<?php
// src/Controller/AdminUserController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminUserController extends Controller
{
    /**
     * Create a new user.
     *
     * @Route("/admin/user", name="admin_user")
     */
    public function adminCreateUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // Will return an active user with ROLE_USER.
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class)
            ->add('username', TextType::class)
            ->add('plainPassword', RepeatedType::class, [
               'type' => PasswordType::class,
               'first_options' => ['label' => 'Password'],
               'second_options' => ['label' => 'Repeat Password'],
            ])
            ->add('save', SubmitType::class, array('label' => 'Create User'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
