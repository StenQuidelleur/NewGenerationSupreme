<?php


namespace App\Controller;


use App\Entity\Contact;
use App\Entity\SubCategory2;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\SubCategory;
use App\Form\ContactType;
use App\Form\UserType;
use App\Repository\AddressRepository;
use App\Repository\BannerRepository;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use App\Repository\NewsRepository;
use App\Repository\OrderProductRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\StockRepository;
use App\Service\Cart\CartService;
use App\Service\search\SearchService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    protected $categories;
    protected $cartService;
    protected $news;

    public function __construct(CategoryRepository $categories, NewsRepository $news, CartService $cartService)
    {
        $this->categories = $categories->findAll();
        $this->cartService = $cartService;
        $this->news = $news->findAll();
    }

    /**
     * @Route("/", name="home_index")
     * @param BannerRepository $banner
     * @param ImageRepository $images
     * @return Response
     */
    public function index(BannerRepository $banner, ImageRepository $images): Response
    {
        $banners = $banner->findAll();
        return $this->render('home/index.html.twig', [
            'categories' => $this->categories,
            'news' => $this->news[0],
            'banners' => $banners
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
            'news' => $this->news[0],
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
        $category = $category->find($subCategory->getCategory()->getId());
        $subCategories = $category->getSubCategories();
        $subCategs2 = $subCategory->getSubCategory2s();
        $products = $subCategory->getProducts();

        return $this->render('home/subCategory.html.twig', [
            'categories' => $this->categories,
            'news' => $this->news[0],
            'products' => $products,
            'category' => $category,
            'subCategories' => $subCategories,
            'subCateg' => $subCategory,
            'subCategs2' => $subCategs2
        ]);
    }

    /**
     * @Route("/subCategory2/{id}", name="subCategory2_index")
     * @param SubCategory2 $subCategory2
     * @param CategoryRepository $category
     * @return Response
     */
    public function getSubcategory2(SubCategory2 $subCategory2, CategoryRepository $category): Response
    {
        $category = $category->find($subCategory2->getSubcategory()->getCategory()->getId());
        $subCategories = $category->getSubCategories();
        $subCategs2 = $subCategory2->getSubcategory()->getSubCategory2s();
        $products = $subCategory2->getProducts();

        return $this->render('home/subCategory.html.twig', [
            'categories' => $this->categories,
            'news' => $this->news[0],
            'products' => $products,
            'category' => $category,
            'subCategories' => $subCategories,
            'subCateg' => $subCategory2->getSubcategory(),
            'subCategs2' => $subCategs2
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_index")
     * @param Product $product
     * @param StockRepository $stock
     * @return Response
     */
    public function getProduct(Product $product, StockRepository $stock): Response
    {
        $sizes = [];
        foreach ($product->getStocks() as $item) {
            if ($item->getQuantity() > 0 && $item->getSize() != null) {
                $sizes[] = $item->getSize()->getName();
            }
        }

        //Add product with his stockID
        $stock = $stock->findBy(['product' => $product->getId()]);
        $stockId = $stock[0]->getId();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stockId = null;
            foreach ($stock as $item){
                if($item->getSize()->getName() == $_POST['size']){
                    $stockId = $item->getId();
                }
            }
            $this->cartService->add($stockId);
            $this->addFlash('white', 'Cet article a été ajouté à votre panier. ');
            return $this->redirectToRoute('product_index', ['id' => $product->getId()]);
        }

        return $this->render('home/product.html.twig', [
            'categories' => $this->categories,
            'news' => $this->news[0],
            'product' => $product,
            'subCateg' => $product->getSubCategory(),
            'subCateg2' => $product->getSubCategory2(),
            'sizes' => $sizes,
            'stockId' => $stockId
        ]);
    }

    /**
     * @Route("/profile", name="profile")
     * @param AddressRepository $address
     * @param Request $request
     * @param OrderRepository $order
     * @param OrderProductRepository $orderProduct
     * @return Response
     */
    public function profile(AddressRepository $address, Request $request, OrderRepository $order, OrderProductRepository $orderProduct): Response
    {
        $shippingAddress = $address->findOneBy(['user' => $this->getUser(),'shipping_address' => 1]);
        $billingAddress = $address->findOneBy(['user' => $this->getUser(),'billing_address' => 1]);
        $orders = $order->findBy(['user' => $this->getUser()]);
        $orderProducts = null;
        foreach ($orders as $orderUser) {
            $orderProducts = $orderProduct->findBy(['order_user' => $orderUser->getId()]);
        }
        return $this->render('home/profile.html.twig', [
            'categories' => $this->categories,
            'news' => $this->news[0],
            'user' => $this->getUser(),
            'shippingAddress' => $shippingAddress,
            'billingAddress' => $billingAddress,
            'items' => $orderProducts,
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
            'news' => $this->news[0]
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
            'news' => $this->news[0],
            'products' => $products
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = (new TemplatedEmail())
                ->from('sten.test4php@gmail.com')
                ->to('sten.test4php@gmail.com')
                ->subject($contact->getObject())
                ->htmlTemplate('email/contact.html.twig')
                ->context(['contact' => $contact]);
            $mailer->send($email);
            //$this->addFlash('success', 'Your email has been sent !');

            return $this->redirectToRoute('home_index');
        }

        return $this->render('home/contact.html.twig',[
            'form' => $form->createView(),
            'categories' => $this->categories,
            'news' => $this->news[0]
        ]);
    }
}