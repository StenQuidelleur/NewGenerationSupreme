<?php


namespace App\Controller;


use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home_index")
     * @param CategoryRepository $category
     * @return Response
     */
    public function index(CategoryRepository $category): Response
    {
        $categories = $category->findAll();
        return $this->render('home/index.html.twig', [
            'categories' => $categories,
        ]);
    }

}