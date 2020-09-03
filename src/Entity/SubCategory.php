<?php

namespace App\Entity;

use App\Repository\SubCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=SubCategoryRepository::class)
 * @Vich\Uploadable
 */
class SubCategory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="subCategories")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="subCategory", cascade={"remove"})
     */
    private $products;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="subCategory", fileNameProperty="image")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=SubCategory2::class, mappedBy="subCategory", cascade={"remove"})
     */
    private $subCategory2s;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->subCategory2s = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setSubCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getSubCategory() === $this) {
                $product->setSubCategory(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }


    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @return Collection|SubCategory2[]
     */
    public function getSubCategory2s(): Collection
    {
        return $this->subCategory2s;
    }

    public function addSubCategory2(SubCategory2 $subCategory2): self
    {
        if (!$this->subCategory2s->contains($subCategory2)) {
            $this->subCategory2s[] = $subCategory2;
            $subCategory2->setSubcategory($this);
        }

        return $this;
    }

    public function removeSubCategory2(SubCategory2 $subCategory2): self
    {
        if ($this->subCategory2s->contains($subCategory2)) {
            $this->subCategory2s->removeElement($subCategory2);
            // set the owning side to null (unless already changed)
            if ($subCategory2->getSubcategory() === $this) {
                $subCategory2->setSubcategory(null);
            }
        }

        return $this;
    }
}
