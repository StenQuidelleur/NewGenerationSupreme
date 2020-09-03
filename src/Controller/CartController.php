<?php


namespace App\Controller;


use App\Repository\CategoryRepository;
use App\Repository\ShippingMethodRepository;
use App\Repository\StockRepository;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    protected $categories;

    public function __construct(CategoryRepository $categories)
    {
        $this->categories = $categories->findAll();
    }

    /**
     * @Route("/panier", name="cart_index")
     * @param CartService $cartService
     * @param ShippingMethodRepository $shipping
     * @return Response
     */
    public function index(CartService $cartService, ShippingMethodRepository $shipping): Response
    {
        $shipping = $shipping->findAll();
        //dd($cartService->getFullCart());
        return $this->render('cart/index.html.twig', [
            'categories' => $this->categories,
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal(),
            'shipping' => $shipping[0]
        ]);
    }

    /**
     * @Route("/panier/add/{id}", name="cart_add")
     * @param $id
     * @param CartService $cartService
     * @param StockRepository $stock
     * @return Response
     */
    public function add($id, CartService $cartService, StockRepository $stock): Response
    {
        $cartService->add($id);
        $productId = $stock->findOneBy(['id' => $id])->getProduct()->getId();
        $this->addFlash('white', 'Cet article a été ajouté à votre panier. ');
        return $this->redirectToRoute('product_index', ['id' => $productId]);
    }

    /**
     * @Route("/panier/remove/{id}", name="cart_remove")
     * @param $id
     * @param CartService $cartService
     * @return Response
     */
    public function remove($id, CartService $cartService): Response
    {
        $cartService->remove($id);

        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/ajax-addQuantity/{id}", name="ajax-addQuantity", methods={"GET", "POST"})
     * @param $id
     * @param CartService $cartService
     * @return JsonResponse
     */
    public function addQuantity($id, CartService $cartService)
    {
        $cartService->add($id);

        return $this->json([
            'message' => 'Un produit à été ajouté',
            'quantity' => $cartService->getQuantity($id),
            'price' => $cartService->getTotalItem($id),
            'total' => $cartService->getTotal()
        ], 200);
    }

    /**
     * @Route("/ajax-removeQuantity/{id}", name="ajax-removeQuantity", methods={"GET", "POST"})
     * @param $id
     * @param CartService $cartService
     * @return JsonResponse
     */
    public function removeQuantity($id, CartService $cartService)
    {
        $cartService->removeQuantity($id);

        return $this->json([
            'message' => 'Un produit à été retiré',
            'quantity' => $cartService->getQuantity($id),
            'price' => $cartService->getTotalItem($id),
            'total' => $cartService->getTotal()
        ],200);
    }
}