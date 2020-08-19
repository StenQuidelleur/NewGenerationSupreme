<?php


namespace App\Controller;


use App\Entity\User;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\SubCategory;
use App\Form\UserType;
use App\Repository\AddressRepository;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\search\SearchService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
     * @param ProductRepository $product
     * @return Response
     */
    public function getCategory(Category $category, ProductRepository $product): Response
    {
        $subCategories = $category->getSubCategories();
        $items = [];
        foreach ($subCategories as $categ) {
            $items[] = $product->findBy(['subCategory' => $categ]);
        }

        return $this->render('home/category.html.twig', [
            'categories' => $this->categories,
            'category' => $category,
            'subCategories' => $subCategories,
            'items' => $items
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
            'subCategories' => $subCategories,
            'subCateg' => $subCategory

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

    /**
     * @Route("/profile", name="profile")
     * @param AddressRepository $address
     * @param Request $request
     * @param OrderRepository $order
     * @return Response
     */
    public function profile(AddressRepository $address, Request $request, OrderRepository $order): Response
    {
        $shippingAddress = $address->findOneBy(['user' => $this->getUser(),'shipping_address' => 1]);
        $billingAddress = $address->findOneBy(['user' => $this->getUser(),'billing_address' => 1]);
        $orders = $order->findBy(['user' => $this->getUser()]);

        return $this->render('home/profile.html.twig', [
            'categories' => $this->categories,
            'user' => $this->getUser(),
            'shippingAddress' => $shippingAddress,
            'billingAddress' => $billingAddress,
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/editProfile/{id}", name="profile_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function editProfile(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profile');
        }

        return $this->render('home/editProfile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'categories' => $this->categories,
        ]);
    }

    /**
     * @Route("/searchProduct", name="product_search")
     * @param SearchService $search
     * @return Response
     */
    public function searchProduct(SearchService $search): Response
    {
        $products = null;
        $searchEmpty = 'Désolé nous n\'avons pas trouvé ce que vous cherchez !';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST['name'];
            $products = $search->searchByTagProduct($data);
        } else {
            $products = 'produits';//$this->getDoctrine()->getRepository(Product::class)->findAll();
        }

        return $this->render('home/search.html.twig', [
            'categories' => $this->categories,
            'products' => $products
        ]);
    }
}