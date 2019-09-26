<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use http\Env\Response;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends Controller
{
    /**
     * @Route("/posts/index", name="posts")
     * @param Request $request
     * @param PaginationInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $postid = $request->query->get('postid');
        $em = $this->getDoctrine()->getRepository(Post::class);


        if($postid != null) {
            $posts = $em->find($postid);
            return $this->render('post/post.html.twig', [
                'controller_name' => 'PostController',
                'post' => $posts
            ]);
        }
        else
            $posts = $em->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $em->findAll(),
            $request->query->getInt('page',1),
            $request->query->getInt('limit',5)
        );


        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/posts/create", name="create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $form = $this->createForm(PostType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $entityManager->persist($data);
            $entityManager->flush();

            unset($form);
            $form = $this->createForm(PostType::class);
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView(),

        ]);
    }
}
