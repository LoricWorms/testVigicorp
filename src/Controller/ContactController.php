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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class ContactController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function accueil()
    {
        return $this->redirectToRoute('app_contact');
    }

    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, SluggerInterface $slugger, ParameterBagInterface $params): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactForm::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Gestion du fichier uploadé
            $uploadedFile = $form->get('fichier')->getData();
            if ($uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
                try {
                    $uploadedFile->move(
                        $params->get('uploads_directory'),
                        $newFilename
                    );
                    // Enregistre le nom dans l’entité Contact
                    $contact->setFichier($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload du fichier.');
                }
            }

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

            // Ajouter le fichier en pièce jointe si présent
            if (isset($newFilename)) {
                $email->attachFromPath($params->get('uploads_directory') . '/' . $newFilename);
            }

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
