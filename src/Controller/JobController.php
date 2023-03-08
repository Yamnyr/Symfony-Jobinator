<?php

namespace App\Controller;

use App\Entity\Job;
use App\Form\JobType;
use App\Repository\JobRepository;
use App\Security\Voter\JobVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/job')]
class JobController extends AbstractController
{
    #[Route('/', name: 'app_job_public_index', methods: ['GET'])]
    public function index(Request $request, JobRepository $jobRepository): Response
    {
        $jobsQueryBuilder = $jobRepository->createQueryBuilder('j')
            ->where('j.published = true')
            ->orderBy('j.createdAt', 'DESC');

        $search = $request->query->get('search');

        if (!empty($search)) {
            $jobsQueryBuilder
                ->andWhere('LOWER(j.title) LIKE :search OR LOWER(j.description) LIKE :search')
                ->setParameter('search', '%'.mb_strtolower($search).'%');
        }

        $jobs = $jobsQueryBuilder
            ->getQuery()
            ->getResult();

        return $this->render('job/public_index.html.twig', [
            'jobs' => $jobs,
        ]);
    }

    #[Route('/my', name: 'app_job_my_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function myIndex(JobRepository $jobRepository): Response
    {
        $jobs = $jobRepository->createQueryBuilder('j')
            ->where('j.owner = :owner')
            ->setParameter('owner', $this->getUser())
            ->orderBy('j.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('job/my_index.html.twig', [
            'jobs' => $jobs,
        ]);
    }

    #[Route('/new', name: 'app_job_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, JobRepository $jobRepository): Response
    {
        $job = new Job();
        $job->setOwner($this->getUser());

        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $jobRepository->save($job, true);

            return $this->redirectToRoute('app_job_my_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('job/new.html.twig', [
            'job' => $job,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_job_show', methods: ['GET'])]
    #[IsGranted(JobVoter::SHOW, subject: 'job')]
    public function show(Job $job): Response
    {
        /*if (false === $job->isPublished()) {
            throw $this->createAccessDeniedException();
        }*/

        return $this->render('job/show.html.twig', [
            'job' => $job,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_job_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    #[IsGranted(JobVoter::EDIT, subject: 'job')]
    public function edit(Request $request, Job $job, JobRepository $jobRepository): Response
    {
        /*if ($job->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }*/

        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $jobRepository->save($job, true);

            return $this->redirectToRoute('app_job_my_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('job/edit.html.twig', [
            'job' => $job,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_job_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    #[IsGranted(JobVoter::DELETE, subject: 'job')]
    public function delete(Request $request, Job $job, JobRepository $jobRepository): Response
    {
        /*if ($job->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }*/

        if ($this->isCsrfTokenValid('delete'.$job->getId(), $request->request->get('_token'))) {
            $jobRepository->remove($job, true);
        }

        return $this->redirectToRoute('app_job_my_index', [], Response::HTTP_SEE_OTHER);
    }
}
