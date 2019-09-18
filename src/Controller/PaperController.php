<?php
// src/Controller/PaperController.php

namespace App\Controller;

use App\Entity\Paper;
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
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Class PaperController
 *
 * Deals with managing papers.
 *
 * @package App\Controller
 */
class PaperController extends AbstractController
{
    /**
     * Make a new paper.
     *
     * @Route("/paper", name="paper")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createPaper(Request $request) : Response
    {
        $paper = new Paper();
        $form = $this->createFormBuilder($paper)
            ->add('paperFile', VichImageType::class, ['label' => 'Upload file here'])
            ->add('save', SubmitType::class, ['label' => 'Save paper'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $user->addPaper($paper);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($paper);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('user.html.twig', ['form' => $form->createView()]);
    }
}
