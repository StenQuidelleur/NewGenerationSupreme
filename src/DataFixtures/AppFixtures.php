<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Product;
use App\Entity\ShippingMethod;
use App\Entity\Status;
use App\Entity\Stock;
use App\Entity\SubCategory;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $categories = ['Skate','Vêtements','Accessoires','Protections','Graffiti','Goodies'];
        $categoriesArray = [];
        foreach ($categories as $item) {
            $category = new Category();
            $category->setName($item);
            $categoriesArray[] = $category;
            $manager->persist($category);
        }

        $subCategoriesArray = [];
        for ($i=0; $i>30; $i++) {
            $subCategory = new SubCategory();
            $subCategory->setName($faker->name);
            $subCategory->setCategory($faker->randomElements($categoriesArray));
            $subCategoriesArray[] = $subCategory;
            $manager->persist($subCategory);
        }

        $shippingMethod = new ShippingMethod();
        $shippingMethod->setType('Colissimo');
        $shippingMethod->setPrice(5);
        $manager->persist($shippingMethod);

        $productsArray = [];
        for ($i=0; $i>50; $i++) {
            $product = new Product();
            $product->setName($faker->name);
            $product->setCategory($faker->randomElements($categoriesArray));
            $product->setPrice($faker->numberBetween(3,100));
            $product->setDescription($faker->text);
            $productsArray[] = $product;
            $manager->persist($product);
        }

        for ($i=0; $i>50; $i++) {
            $stock = new Stock();
            $stock->setProduct($faker->randomElements($productsArray));
            $stock->setMinimum(10);
            $stock->setQuantity(30);
            $manager->persist($stock);
        }

        for ($i=0; $i>100; $i++) {
            $image = new Image();
            $image->setProduct($faker->randomElements($productsArray));
            $image->setName($faker->imageUrl(300,300));
            $manager->persist($image);
        }

        $user = new User();
        $user->setFirstname("Sten");
        $user->setLastname("Quidelleur");
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setIsVerified(1);
        $user->setRegistrationDate($faker->dateTime);
        $user->setEmail('sten@gmail.com');
        $user->setPassword($this->encoder->encodePassword($user,'azerty'));
        $manager->persist($user);

        $status = ['Validée', 'Préparation', 'Expédiée'];
        foreach ($status as $item) {
            $status = new Status();
            $status->setName($item);
            $manager->persist($status);
        }

        $manager->flush();
    }
}
