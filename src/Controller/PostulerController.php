<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PostulerFormType;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class PostulerController extends AbstractController
{
    #[Route('/postuler/{id}', name: 'app_postuler')]
    public function postuler(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, User $user, JobRepository $jobRepository, int $id): Response
    {
        $form = $this->createForm(PostulerFormType::class);
        $form->handleRequest($request);

        $job = $jobRepository->findOneById($id);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = (new TemplatedEmail())
                ->to($user->getEmail())
                ->subject('Nouvelle candidature')
                ->textTemplate('emails/welcome.txt.twig')
                ->context([
                    'name' => $form->get('nom')->getData(),
                    'prenom' => $form->get('prenom')->getData(),
                    'email' => $form->get('email')->getData(),
                    'cv' => $form->get('cv')->getData(),
                    'job' => $job,
                ]);

            $mailer->send($email);

            return $this->redirectToRoute('app_job_show', ['id' => $id]);
        }

        return $this->render('postuler/index.html.twig', [
            'postulerForm' => $form->createView(),
        ]);
    }
}
