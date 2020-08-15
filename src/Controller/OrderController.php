<?php


namespace App\Controller;


use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use App\Repository\CategoryRepository;
use App\Repository\ShippingMethodRepository;
use App\Service\Cart\CartService;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    protected $categories;
    protected $session;

    public function __construct(CategoryRepository $categories, SessionInterface $session)
    {
        $this->categories = $categories->findAll();
        $this->session = $session;
    }

    /**
     * @Route("/order", name="order_index")
     * @param Request $request
     * @param CartService $cartService
     * @param ShippingMethodRepository $shipping
     * @param AddressRepository $address
     * @return Response
     */
    public function index(Request $request, CartService $cartService, ShippingMethodRepository $shipping, AddressRepository $address): Response
    {
        $shippingAddress = $address->findOneBy(['user' => $this->getUser(),'shipping_address' => 1]);
        $billingAddress = $address->findOneBy(['user' => $this->getUser(),'billing_address' => 1]);
        $infoClient = $this->session->get('infoClient', null);

        $contact = new Address();
        $form = $this->createForm(AddressType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $infoClient = $this->session->set('infoClient', $contact);

            return $this->redirectToRoute('payment');
        }

        $shipping = $shipping->findAll();
        return $this->render('order/index.html.twig', [
            'categories' => $this->categories,
            'form' => $form->createView(),
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal(),
            'shipping' => $shipping[0],
            'shippingAddress' => $shippingAddress,
            'billingAddress' => $billingAddress
        ]);
    }

    /**
     * @Route("/payment", name="payment")
     * @param AddressRepository $address
     * @param CartService $cartService
     * @param ShippingMethodRepository $shipping
     * @return Response
     * @throws ApiErrorException
     */
    public function payment(AddressRepository $address, CartService $cartService, ShippingMethodRepository $shipping): Response
    {
        $shippingAddress = $address->findOneBy(['user' => $this->getUser(),'shipping_address' => 1]);
        $billingAddress = $address->findOneBy(['user' => $this->getUser(),'billing_address' => 1]);
        if ($this->getUser() != null && $shippingAddress == null) {
            $shippingAddress = $billingAddress;
        }
        $infoClient = $this->session->get('infoClient', null);
        $shipping = $shipping->findAll();
        $total = $cartService->getTotal() + $shipping[0]->getPrice();

        \Stripe\Stripe::setApiKey('sk_test_51HG5BSDtJEs1xZocO9uG5lzD2uWOKRiTyCHEHLa7KAZ001VaWZnGinFTmqrTT9vtRPhPGaMzNH3xpYfeE0SRLadP00YJZgWg72');
        $intent = \Stripe\PaymentIntent::create([
            'amount' => $total*100,
            'currency' => 'eur'
        ]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $cartService->removeAll();

            return $this->redirectToRoute('home_index');
        }

        return $this->render('order/payment.html.twig', [
            'categories' => $this->categories,
            'user' => $this->getUser(),
            'shippingAddress' => $shippingAddress,
            'billingAddress' => $billingAddress,
            'items' => $cartService->getFullCart(),
            'totalArticle' => $cartService->getTotal(),
            'total' => $total,
            'shipping' => $shipping[0],
            'infoClient' => $infoClient,
            'intent' => $intent
        ]);
    }
}