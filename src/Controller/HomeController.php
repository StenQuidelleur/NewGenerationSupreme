<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Product;
use App\Entity\SubCategory;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    protected $categories;

    public function __construct(CategoryRepository $categories)
    {
        $this->categories = $categories->findAll();
    }

    /**
     * @Route("/", name="home_index")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'categories' => $this->categories,
        ]);
    }

    /**
     * @Route("/category/{id}", name="category_index")
     * @param Category $category
     * @return Response
     */
    public function getCategory(Category $category): Response
    {
        $subCategories = $category->getSubCategories();
        return $this->render('home/category.html.twig', [
            'categories' => $this->categories,
            'category' => $category,
            'subCategories' => $subCategories
        ]);
    }

    /**
     * @Route("/subCategory/{id}", name="subCategory_index")
     * @param SubCategory $subCategory
     * @param CategoryRepository $category
     * @return Response
     */
    public function getSubcategory(SubCategory $subCategory, CategoryRepository $category): Response
    {
        $categoryId = $subCategory->getCategory()->getId();
        $category = $category->find($categoryId);
        $subCategories = $category->getSubCategories();

        $products = $subCategory->getProducts();

        return $this->render('home/subCategory.html.twig', [
            'categories' => $this->categories,
            'products' => $products,
            'category' => $category,
            'subCategories' => $subCategories
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_index")
     * @param Product $product
     * @return Response
     */
    public function getProduct(Product $product): Response
    {
        return $this->render('home/product.html.twig', [
            'categories' => $this->categories,
            'product' => $product,
        ]);
    }
}