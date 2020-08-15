<?php

namespace App\Controller;


use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/address")
 */
class AddressController extends AbstractController
{
    protected $categories;

    public function __construct(CategoryRepository $categories)
    {
        $this->categories = $categories->findAll();
    }

    /**
     * @Route("/", name="address_index", methods={"GET"})
     * @param AddressRepository $addressRepository
     * @return Response
     */
    public function index(AddressRepository $addressRepository): Response
    {
        return $this->render('address/index.html.twig', [
            'addresses' => $addressRepository->findAll(),
        ]);
    }

    /**
     * @Route("/newBillig", name="address_newBilling", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function newBilling(Request $request): Response
    {
        $billingAddress = new Address();
        $form = $this->createForm(AddressType::class, $billingAddress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $billingAddress->setUser($this->getUser());
            $billingAddress->setBillingAddress(1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($billingAddress);
            $entityManager->flush();

            return $this->redirectToRoute('profile');
        }

        return $this->render('address/newBilling.html.twig', [
            'address' => $billingAddress,
            'form' => $form->createView(),
            'categories' => $this->categories,
        ]);
    }

    /**
     * @Route("/newShipping", name="address_newShipping", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function newShipping(Request $request): Response
    {
        $shippingAddress = new Address();
        $form = $this->createForm(AddressType::class, $shippingAddress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shippingAddress->setUser($this->getUser());
            $shippingAddress->setShippingAddress(1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($shippingAddress);
            $entityManager->flush();

            return $this->redirectToRoute('profile');
        }

        return $this->render('address/newShipping.html.twig', [
            'address' => $shippingAddress,
            'form' => $form->createView(),
            'categories' => $this->categories,
        ]);
    }

    /**
     * @Route("/{id}", name="address_show", methods={"GET"})
     */
    public function show(Address $address): Response
    {
        return $this->render('address/show.html.twig', [
            'address' => $address,
        ]);
    }

    /**
     * @Route("/{id}/editBilling", name="billingAddress_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Address $address
     * @return Response
     */
    public function editBilling(Request $request, Address $address): Response
    {
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $address->setBillingAddress(1);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profile');
        }

        return $this->render('address/editBilling.html.twig', [
            'address' => $address,
            'form' => $form->createView(),
            'categories' => $this->categories,
        ]);
    }

    /**
     * @Route("/{id}/editShipping", name="shippingAddress_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Address $address
     * @return Response
     */
    public function edit(Request $request, Address $address): Response
    {
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $address->setShippingAddress(1);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profile');
        }

        return $this->render('address/editShipping.html.twig', [
            'address' => $address,
            'form' => $form->createView(),
            'categories' => $this->categories,
        ]);
    }

    /**
     * @Route("/{id}", name="address_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Address $address): Response
    {
        if ($this->isCsrfTokenValid('delete'.$address->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($address);
            $entityManager->flush();
        }

        return $this->redirectToRoute('address_index');
    }
}
