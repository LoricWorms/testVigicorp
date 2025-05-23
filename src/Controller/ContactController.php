<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contact;
use App\Form\ContactForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactForm::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Enregistrement en base de données
            $entityManager->persist($contact);
            $entityManager->flush();

            // Création de l'email de confirmation
            $email = (new Email())
                ->from('hello@example.com')
                ->to($contact->getEmail())
                ->subject('Confirmation de votre formulaire de contact')
                ->text(
                    sprintf(
                        "Bonjour %s %s,\n\nMerci pour votre message. Voici un récapitulatif de vos informations :\n\nNom : %s\nPrénom : %s\nEmail : %s\n\nCordialement,\nL'équipe",
                        $contact->getPrenom(),
                        $contact->getNom(),
                        $contact->getNom(),
                        $contact->getPrenom(),
                        $contact->getEmail()
                    )
                );

            try {
                $mailer->send($email);
                $this->addFlash('success', 'Votre message a bien été envoyé et un email de confirmation vous a été adressé.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
            }

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
