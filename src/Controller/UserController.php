<?php
// src/Controller/UserController.php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController
 *
 * Deals wth creating and editing users.
 *
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /**
     * Create a new user.
     *
     * @Route("/admin/user", name="admin_user")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     */
    public function adminCreateUser(Request $request, UserPasswordEncoderInterface $passwordEncoder) : Response
    {
        // Will return an active user with ROLE_USER.
        $new_user = new User();
        $new_user_form = $this->createFormBuilder($new_user)
            ->add('email', EmailType::class)
            ->add('username', TextType::class)
            ->add('plainPassword', RepeatedType::class, [
               'type' => PasswordType::class,
               'first_options' => ['label' => 'Password'],
               'second_options' => ['label' => 'Repeat Password'],
            ])
            ->add('save', SubmitType::class, array('label' => 'Create User'))
            ->getForm();

        $new_user_form->handleRequest($request);

        if ($new_user_form->isSubmitted() && $new_user_form->isValid()) {
            $new_user->setPassword(
                $passwordEncoder->encodePassword(
                    $new_user,
                    $new_user_form->get('plainPassword')->getData()
                )
            );

            $entity_manager = $this->getDoctrine()->getManager();
            $entity_manager->persist($new_user);
            $entity_manager->flush();

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('user.html.twig', ['form' => $new_user_form->createView()]);
    }

    /**
     * Edit user parameters.
     *
     * No password edit here.
     *
     * @Route("/admin/user/{id}", name="admin_user_edit")
     *
     * @param string $id Id of the user to edit
     * @param Request $request
     * @param EntityManagerInterface $entity_manager
     *
     * @return Response
     */
    public function adminEditUser(
        string $id,
        Request $request,
        EntityManagerInterface $entity_manager
    ) : Response {
        // Will load user by Id.
        $edit_user = $entity_manager->getRepository(User::class)->findOneBy(['id' => $id]);
        $edit_user_form = $this->createFormBuilder($edit_user)
            ->add('email', EmailType::class)
            ->add('username', TextType::class)
            ->add('isActive', CheckboxType::class, ['required' => false])
            ->add('save', SubmitType::class, array('label' => 'Save changes'))
            ->getForm();

        $edit_user_form->handleRequest($request);

        if ($edit_user_form->isSubmitted() && $edit_user_form->isValid()) {
            $entity_manager = $this->getDoctrine()->getManager();
            $entity_manager->persist($edit_user);
            $entity_manager->flush();

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('user.html.twig', ['form' => $edit_user_form->createView()]);
    }

    /**
     * Change user password.
     *
     * No profile edit here.
     *
     * @Route("/admin/user/password/{id}", name="admin_password_change")
     *
     * @param string $id Id of the user to change password on
     * @param Request $request
     * @param EntityManagerInterface $entity_manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     */
    public function adminChangePassword(
        string $id,
        Request $request,
        EntityManagerInterface $entity_manager,
        UserPasswordEncoderInterface $passwordEncoder
    ) : Response {
        // Will load user by Id.
        $change_password_user = $entity_manager->getRepository(User::class)->findOneBy(['id' => $id]);
        $change_password_user_form = $this->createFormBuilder($change_password_user)
            ->add('email', EmailType::class, [
                'attr' => ['disabled' => true, 'value' => $change_password_user->getEmail()],
                'mapped' => false
            ])
            ->add('username', TextType::class, [
                'attr' => ['disabled' => true, 'value' => $change_password_user->getUsername()],
                'mapped' => false
            ])
            ->add('isActive', CheckboxType::class, [
                'attr' => ['disabled' => true, 'checked' => $change_password_user->getIsActive()],
                'mapped' => false
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            ->add('save', SubmitType::class, ['label' => 'Change password'])
            ->getForm();

        $change_password_user_form->handleRequest($request);

        if ($change_password_user_form->isSubmitted() && $change_password_user_form->isValid()) {
            $change_password_user->setPassword(
                $passwordEncoder->encodePassword(
                    $change_password_user,
                    $change_password_user->getPlainPassword()
                )
            );
            $entity_manager = $this->getDoctrine()->getManager();
            $entity_manager->persist($change_password_user);
            $entity_manager->flush();

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('user.html.twig', ['form' => $change_password_user_form->createView()]);
    }

    /**
     * List users.
     *
     * @Route("/admin/users", name="admin_users")
     *
     * @param Request $request
     * @param EntityManagerInterface $entity_manager
     *
     * @return Response
     */
    public function adminListUsers(
        Request $request,
        EntityManagerInterface $entity_manager
    ) : Response {
        $all_users = $entity_manager->getRepository(User::class)->findAll();
        return $this->render('users.html.twig', ['users' => $all_users]);
    }

    /**
     * Registers our user class as the data class.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }
}
