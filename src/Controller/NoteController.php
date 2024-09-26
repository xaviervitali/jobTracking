<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\JobRepository;
use App\Repository\NoteRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Serializer;

#[Route('/note')]
final class NoteController extends AbstractController
{
    #[Route(name: 'app_note_index', methods: ['GET'])]
    public function index(NoteRepository $noteRepository): Response
    {
        return $this->render('note/index.html.twig', [
            'notes' => $noteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_note_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $note = new Note();

        $form = $this->createForm(NoteType::class, $note);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $note
                ->setCreatedAt(new DateTimeImmutable())
                ->setUser($security->getUser());
                $entityManager->persist($note);
                $entityManager->flush();
        }

        return $this->redirectToRoute('app_job_tracking', ['id'=> $note->getJob()->getId()]);

    }

    #[Route('/{id}', name: 'app_note_show', methods: ['GET'])]
    public function show(Note $note): Response
    {
        return $this->render('note/show.html.twig', [
            'note' => $note,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_note_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Note $note, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('note/edit.html.twig', [
            'note' => $note,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_note_delete')]
    public function delete(Note $note, EntityManagerInterface $entityManager, Security $security): Response
    {
        $job = $note->getJob();
        $user = $security->getUser();
        if ($note->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimé cette note.');
        }

        $entityManager->remove($note);
        $entityManager->flush();

        return $this->redirectToRoute('app_job_tracking', ['id' => $job->getId()], Response::HTTP_SEE_OTHER);
    }
}
