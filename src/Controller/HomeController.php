<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Product;
use App\Entity\SubCategory;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home_index")
     * @param CategoryRepository $categories
     * @return Response
     */
    public function index(CategoryRepository $categories): Response
    {
        $categories = $categories->findAll();
        return $this->render('home/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/category/{id}", name="category_index")
     * @param Category $category
     * @param CategoryRepository $categories
     * @return Response
     */
    public function getCategory(Category $category, CategoryRepository $categories): Response
    {
        $categories = $categories->findAll();
        $subCategories = $category->getSubCategories();
        return $this->render('home/category.html.twig', [
            'categories' => $categories,
            'category' => $category,
            'subCategories' => $subCategories
        ]);
    }

    /**
     * @Route("/subCategory/{id}", name="subCategory_index")
     * @param SubCategory $subCategory
     * @param CategoryRepository $categories
     * @param CategoryRepository $category
     * @return Response
     */
    public function getSubcategory(SubCategory $subCategory, CategoryRepository $categories, CategoryRepository $category): Response
    {

        $categoryId = $subCategory->getCategory()->getId();
        $category = $category->find($categoryId);
        $subCategories = $category->getSubCategories();

        $products = $subCategory->getProducts();

        $categories = $categories->findAll();

        return $this->render('home/subCategory.html.twig', [
            'categories' => $categories,
            'products' => $products,
            'category' => $category,
            'subCategories' => $subCategories
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_index")
     * @param Product $product
     * @param CategoryRepository $categories
     * @return Response
     */
    public function getProduct(Product $product, CategoryRepository $categories): Response
    {
        $categories = $categories->findAll();

        return $this->render('home/product.html.twig', [
            'categories' => $categories,
            'product' => $product
        ]);
    }
}