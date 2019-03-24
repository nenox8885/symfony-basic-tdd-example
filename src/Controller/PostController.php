<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    private $postRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        PostRepository $postRepository
    ) {
        $this->entityManager = $entityManager;
        $this->postRepository = $postRepository;
    }

    public function cgetAction()
    {
        return $this->view($this->postRepository->findAll());
    }

    public function getAction(string $id)
    {
        return $this->view($this->findPostById($id));
    }
    
    public function postAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        
        $form->submit($request->request->all());

        if (false === $form->isValid()) {
            return $this->view($form, Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();

        return $this->view($post, Response::HTTP_CREATED);
        
    }

    public function putAction(Request $request, string $id)
    {
        $post = $this->findPostById($id);
        $form = $this->createForm(PostType::class, $post);
        
        $form->submit($request->request->all());

        if (false === $form->isValid()) {
            return $this->view($form, Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    public function deleteAction(string $id)
    {
        $post = $this->findPostById($id);

        $this->entityManager->remove($post);
        $this->entityManager->flush();

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    private function findPostById($id)
    {
        $post = $this->postRepository->find($id);

        if (null === $post) {
            throw new NotFoundHttpException();
        }

        return $post;
    }
}
