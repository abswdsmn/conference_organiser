<?php
// src/Controller/EventController.php

namespace App\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
 * Class EventController
 *
 * Deals wth creating and editing events.
 *
 * @package App\Controller
 */
class EventController extends AbstractController
{
    /**
     * Create a new event.
     *
     * @Route("/admin/event", name="admin_event")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function adminCreateEvent(Request $request) : Response
    {
        $new_event = new Event();
        $new_event_form = $this->createFormBuilder($new_event)
            ->add('title', TextType::class, ['required' => false])
            ->add('description', TextType::class, ['required' => false])
            ->add('date', DateTimeType::class, ['required' => false, 'input' => 'datetime_immutable'])
            ->add('address', TextType::class, ['required' => false])
            ->add('postcode', TextType::class, ['required' => false])
            ->add('save', SubmitType::class, ['label' => 'Create Event'])
            ->getForm();

        $new_event_form->handleRequest($request);

        if ($new_event_form->isSubmitted() && $new_event_form->isValid()) {
            $entity_manager = $this->getDoctrine()->getManager();
            $entity_manager->persist($new_event);
            $entity_manager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('event.html.twig', ['form' => $new_event_form->createView()]);
    }

    /**
     * Edit event parameters.
     *
     * @Route("/admin/event/{id}", name="admin_event_edit")
     *
     * @param string $id Id of the event to edit
     * @param Request $request
     * @param EntityManagerInterface $entity_manager
     *
     * @return Response
     */
    public function adminEditEvent(
        string $id,
        Request $request,
        EntityManagerInterface $entity_manager
    ) : Response {
        // Will load event by Id.
        $edit_event = $entity_manager->getRepository(Event::class)->findOneBy(['id' => $id]);
        $edit_event_form = $this->createFormBuilder($edit_event)
            ->add('title', TextType::class, ['required' => false])
            ->add('description', TextType::class, ['required' => false])
            ->add('date', DateTimeType::class, ['required' => false, 'input' => 'datetime_immutable'])
            ->add('address', TextType::class, ['required' => false])
            ->add('postcode', TextType::class, ['required' => false])
            ->add('save', SubmitType::class, ['label' => 'Save changes'])
            ->getForm();

        $edit_event_form->handleRequest($request);

        if ($edit_event_form->isSubmitted() && $edit_event_form->isValid()) {
            $entity_manager = $this->getDoctrine()->getManager();
            $entity_manager->persist($edit_event);
            $entity_manager->flush();

            return $this->redirectToRoute('admin_events');
        }

        return $this->render('event.html.twig', ['form' => $edit_event_form->createView()]);
    }

    /**
     * List events.
     *
     * @Route("/admin/events", name="admin_events")
     *
     * @param Request $request
     * @param EntityManagerInterface $entity_manager
     *
     * @return Response
     */
    public function adminListEvents(
        Request $request,
        EntityManagerInterface $entity_manager
    ) : Response {
        $all_events = $entity_manager->getRepository(Event::class)->findAll();
        return $this->render('events.html.twig', ['events' => $all_events]);
    }
}
