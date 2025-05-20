<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Form\TaskTypeFormType;
use Doctrine\ORM\EntityManagerInterface;

final class TaskController extends AbstractController
{   
    
    #[Route('/tasks', name: 'app_home', methods:['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        // Get all tasks
        $tasks = $taskRepository->findAll();
        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/task/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {
        // Renderitzar la vista amb la tasca específica
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/task/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskTypeFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Tasca actualitzada correctament');
            return $this->redirectToRoute('app_task_show', ['id' => $task->getId()]);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/task/{id}/delete', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
            $this->addFlash('success', 'Tasca eliminada correctament');
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/tasks/create', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        // Establecer la fecha de creación actual
        $task->setCreatedAt(new \DateTimeImmutable());

        // Obtener el usuario actual y asignarlo a la tarea
        $user = $this->getUser();
        if (!$user) {
            // Si no hay usuario autenticado, redirigir al login
            $this->addFlash('error', 'Heu d\'iniciar sessió per crear tasques');
            return $this->redirectToRoute('app_login'); // Ajusta esta ruta según tu configuración
        }
        // Asignar el usuario a la tarea
        $task->setUser($user);
        
        $form = $this->createForm(TaskTypeFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'Tasca creada correctament');
            return $this->redirectToRoute('app_task_show', ['id' => $task->getId()]);
        }

        return $this->render('task/create.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }
}