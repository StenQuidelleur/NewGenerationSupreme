<?php

namespace App\Service\Cart;

use App\Repository\ProductRepository;
use App\Repository\StockRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected $session;
    protected $stock;
    public function __construct(SessionInterface $session, StockRepository $stock)
    {
        $this->session = $session;
        $this->stock = $stock;
    }

    public function add(int $id)
    {
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $this->session->set('panier', $panier);
    }

    public function remove(int $id)
    {
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $this->session->set('panier',$panier);
    }

    public function removeAll()
    {
        $panier = $this->session->get('panier', []);

        if (!empty($panier)) {
            $panier = [];
        }

        $this->session->set('panier',$panier);
    }

    public function removeQuantity(int $id)
    {
        $panier = $this->session->get('panier', []);
        if (!empty($panier[$id]) && $panier[$id] > 1) {
            $panier[$id]--;
        } else {
            $panier[$id] = 1;
        }
        $this->session->set('panier', $panier);
    }

    public function getFullCart () :array
    {
        $panier = $this->session->get('panier', []);
        $panierData = [];
        foreach ($panier as $id => $quantity) {
            $panierData[]= [
                'stockProduct' => $this->stock->find($id),
                'quantity' => $quantity
            ];
        }
        return $panierData;
    }

    public function getQuantity(int $id)
    {
        return $this->session->get('panier', [])[$id];
    }

    public function getTotalItem(int $id)
    {
        $productPrice = $this->stock->find($id)->getProduct()->getPrice();
        return $productPrice*$this->getQuantity($id);
    }

    public function getTotal() :float
    {
        $total = 0;
        foreach ($this->getFullCart() as $item) {
            $total += $item['stockProduct']->getProduct()->getPrice() * $item['quantity'];;
        }

        return $total;
    }
}