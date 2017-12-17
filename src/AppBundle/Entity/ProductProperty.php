<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ProductProperty
 *
 * @ORM\Entity
 * @ORM\Table(name="product_property")
 */
class ProductProperty
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Product
     *
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="properties")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    protected $value;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="integer")
     */
    protected $sort;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ProductProperty
     */
    public function setId(?int $id): ProductProperty
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return ProductProperty
     */
    public function setProduct(?Product $product): ProductProperty
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ProductProperty
     */
    public function setName(?string $name): ProductProperty
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return ProductProperty
     */
    public function setTitle(?string $title): ProductProperty
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return ProductProperty
     */
    public function setValue(?string $value): ProductProperty
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getSort(): ?int
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     * @return ProductProperty
     */
    public function setSort(?int $sort): ProductProperty
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->getTitle();
    }
}