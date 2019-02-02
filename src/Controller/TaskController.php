<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\TaskType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Date;

class TaskController extends AbstractController
{

    public function index()
    {

        //Prueba de entidades y relaciones

        $em = $this->getDoctrine()->getManager();
        $task_repo = $this->getDoctrine()->getRepository(Task::class);
        $tasks = $task_repo->findBy([], ['id' => 'DESC']);

/*
        $user_repo = $this->getDoctrine()->getRepository(User::class);
        $users = $user_repo->findAll();

        foreach ($users as $user) {
            echo " <h1> {$user->getName()} {$user->getSurname()} </h1>";

            foreach ($user->getTasks() as $task) {
                echo $task->getTitle() . "<br/>";

            }
        }
*/

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    public function detail(Task $task)
    {
        if (!$task){
            return $this->redirectToRoute('tasks');
        }

        return $this->render('task/detail.html.twig', [
            'task' => $task,
        ]);
    }

    public function creation(Request $request, UserInterface $user)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        //unir lo que llega por la petición al objeto
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $task->setCreatedAt(new \DateTime('now'));
            $task->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('task_detail', ['id' => $task->getId()])
            );

        }

        return $this->render('task/creation.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
