<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, FormFactoryInterface $formFactory, MailerInterface $mailer): Response
    {
        $data = new ContactDTO();

        $form = $formFactory->create(ContactType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            try{
                $mail = (new TemplatedEmail())
                        ->to($data->service)
                        ->from($data->email)
                        ->subject('Demande de contact')
                        ->htmlTemplate('emails/contact.html.twig')
                        ->context(['data' => $data]);

                $mailer->send($mail);
                $this->addFlash('success', 'La message a bien ete envoyer');
                return $this->redirectToRoute('contact');
            } catch(\Exception $e) {
                $this->addFlash('danger', 'impossible d\'envoyer votre email'. $e);
            }
                
        }
        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
