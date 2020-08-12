<?php


namespace App\Controller;


use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use App\Repository\CategoryRepository;
use App\Repository\ShippingMethodRepository;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    protected $categories;

    public function __construct(CategoryRepository $categories)
    {
        $this->categories = $categories->findAll();
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
        $form = null;
        if (($this->getUser() != null && $this->getUser()->getAddresses() != null)) {
            $shippingAddress = $address->findOneBy(['user' => $this->getUser(),'shipping_address' => 1]);
            $billingAddress = $address->findOneBy(['user' => $this->getUser(),'billing_address' => 1]);
            $form = $this->createForm(AddressType::class, $billingAddress);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $address->setUser($this->getUser());
                $address->setBillingAddress(1);
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('profile');
            }
        } else {
            $contact = new Address();
            $form = $this->createForm(AddressType::class, $contact);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($request);
                $entityManager->flush();
            }
        }

        $shipping = $shipping->findAll();
        return $this->render('order/index.html.twig', [
            'categories' => $this->categories,
            'form' => $form->createView(),
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal(),
            'shipping' => $shipping[0],
            'shippingAddress' => $shippingAddress
        ]);
    }
}