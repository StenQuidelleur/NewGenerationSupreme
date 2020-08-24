<?php


namespace App\Controller;


use App\Entity\Address;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use App\Repository\CategoryRepository;
use App\Repository\OrderProductRepository;
use App\Repository\ProductRepository;
use App\Repository\ShippingMethodRepository;
use App\Repository\StatusRepository;
use App\Repository\StockRepository;
use App\Service\Cart\CartService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Stripe\Exception\ApiErrorException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    protected $categories;
    protected $session;
    protected $cartService;
    protected $shipping;
    protected $address;

    public function __construct(CategoryRepository $categories, SessionInterface $session, CartService $cartService, ShippingMethodRepository $shipping, AddressRepository $address)
    {
        $this->categories = $categories->findAll();
        $this->session = $session;
        $this->cartService = $cartService;
        $this->shipping = $shipping->findAll();
        $this->address = $address;
    }

    /**
     * @Route("/order", name="order_index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        //Security if cart is empty redirect to home page.
        if ($this->session->get('panier', []) == []) {
            return $this->redirectToRoute('home_index');
        }
        //Render view
        $shippingAddress = $this->address->findOneBy(['user' => $this->getUser(),'shipping_address' => 1]);
        $billingAddress = $this->address->findOneBy(['user' => $this->getUser(),'billing_address' => 1]);
        //Initialize infoClient no-login
        $infoClient = $this->session->get('infoClient', []);

        $contact = new Address();
        $form = $this->createForm(AddressType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser() != null) {
                $contact->setUser($this->getUser());
                $contact->setBillingAddress(1);
                $contact->setShippingAddress(1);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($contact);
                $entityManager->flush();
            } elseif ($this->getUser() == null){
                $infoClient = $this->session->set('infoClient', $contact);
            }

            return $this->redirectToRoute('payment');
        }

        return $this->render('order/index.html.twig', [
            'categories' => $this->categories,
            'form' => $form->createView(),
            'items' => $this->cartService->getFullCart(),
            'total' => $this->cartService->getTotal(),
            'shipping' => $this->shipping[0],
            'shippingAddress' => $shippingAddress,
            'billingAddress' => $billingAddress
        ]);
    }

    /**
     * @Route("/payment", name="payment")
     * @param StatusRepository $status
     * @param OrderProductRepository $items
     * @param StockRepository $stock
     * @param MailerInterface $mailer
     * @return Response
     * @throws ApiErrorException
     * @throws TransportExceptionInterface
     */
    public function payment(StatusRepository $status, OrderProductRepository $items, StockRepository $stock, MailerInterface $mailer): Response
    {
        //Security if cart is empty redirect to home page.
        if ($this->session->get('panier', []) == []) {
            return $this->redirectToRoute('home_index');
        }
        //Render view
        $shippingAddress = $this->address->findOneBy(['user' => $this->getUser(),'shipping_address' => 1]);
        $billingAddress = $this->address->findOneBy(['user' => $this->getUser(),'billing_address' => 1]);
        if ($this->getUser() != null && $shippingAddress == null) {
            $shippingAddress = $billingAddress;
        }
        $infoClient = $this->session->get('infoClient', []);
        $total = $this->cartService->getTotal() + $this->shipping[0]->getPrice();

        //This for the payment with Stripe
        \Stripe\Stripe::setApiKey('sk_test_51HG5BSDtJEs1xZocO9uG5lzD2uWOKRiTyCHEHLa7KAZ001VaWZnGinFTmqrTT9vtRPhPGaMzNH3xpYfeE0SRLadP00YJZgWg72');
        $intent = \Stripe\PaymentIntent::create([
            'amount' => $total*100,
            'currency' => 'eur'
        ]);
        //Generate the order
        $panier = $this->cartService->getFullCart();
        //if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            /*$order = new Order();
            if ($this->getUser() != null) {
                $order->setUser($this->getUser());
            }
            $order->setStatus($status->findOneBy(['name' => 'Validée']));
            $order->setShippingMethod($this->shipping[0]);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($order);
            $entityManager->flush();
            foreach ($panier as $item) {
                $orderProduct = new OrderProduct();
                $orderProduct->setOrderUser($order);
                $orderProduct->setProduct($item['product']);
                $orderProduct->setQuantity($item['quantity']);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($orderProduct);
                $entityManager->flush();
                //Manage Stock
                $stockProduct = $stock->findOneBy(['id' => $item['product']->getStock()]);
                $stockProduct = $stockProduct->setQuantity($stockProduct->getQuantity() - $item['quantity']);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($stockProduct);
                $entityManager->flush();
            }
            //Generate the order
            if ($this->getUser() != null) {
                $infoClientB = $billingAddress;
                $infoClientS = $shippingAddress;
            } else {
                $infoClientB = $this->session->get('infoClient', []);
                $infoClientS = $infoClientB;
            }
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');
            $dompdf = new Dompdf($pdfOptions);
            $items = $items->findBy(['order_user' => $order->getId()]);
            $client = $infoClient->getFirstname().$infoClient->getLastname().$order->getReference();
            $html = $this->renderView('pdf/invoice.html.twig', [
                'infoClientB' => $infoClientB,
                'infoClientS' => $infoClientS,
                'order' => $order,
                'items' => $items,
                'total' => $total,
                'shipping' => $this->shipping[0]
            ]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $output = $dompdf->output();
            $pdfFilepath = 'assets/documents/invoices/invoice'.$client.'.pdf';
            file_put_contents($pdfFilepath, $output);

            $email = (new TemplatedEmail())
                ->from('sten.test4php@gmail.com')
                ->to('sten.test4php@gmail.com')
                ->subject('Votre facture NGS')
                ->htmlTemplate('email/invoice.html.twig')
                ->context(['contact' => $infoClient])
                ->attachFromPath('assets/documents/invoices/invoice'.$client.'.pdf');
            $mailer->send($email);

            $this->cartService->removeAll();

            return $this->redirectToRoute('home_index');
        }*/

        return $this->render('order/payment.html.twig', [
            'categories' => $this->categories,
            'user' => $this->getUser(),
            'shippingAddress' => $shippingAddress,
            'billingAddress' => $billingAddress,
            'items' => $panier,
            'totalArticle' => $this->cartService->getTotal(),
            'total' => $total,
            'shipping' => $this->shipping[0],
            'infoClient' => $infoClient,
            'intent' => $intent
        ]);
    }
}