<?php


namespace App\Service\search;


use App\Entity\Product;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;

class SearchService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function searchByTagProduct($data)
    {
        if ($data === null) {
            return $products = $this->em->getRepository(Product::class)->findAll();
        } else {
            $tag = $this->em->getRepository(Tag::class)->findOneBy(['name' => $data]);
            if ($tag === null) {
                return $products = $this->em->getRepository(Product::class)->findOneBy(['name' => $data]);
            } else {
                return $products = $tag->getProducts();
            }
        }
    }
}