<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/categories/index", name="categories")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $form = $this->createForm(CategoryType::class);

        $form->handleRequest($request);

        $categories = $entityManager->getRepository(Category::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $entityManager->persist($data);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('category_index'));
        }

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'form' => $form->createView(),
            'categories' => $categories
        ]);

    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/delete", name="delete")
     */
    public function delete(Request $request){

        $em = $this->getDoctrine()->getManager();
        $categoryid = $request->query->get('categoryid');

        $category = $em->getRepository(Category::class)->find($categoryid);

        $em->remove($category);
        $em->flush();

        return $this->redirect($this->generateUrl('category_index'));

    }
}
