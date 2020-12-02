<?php
/*
 * Create : php bin/console make:entity
 *
 *
 */

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $intro;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="articles")
     */
    private $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setIntro(string $intro): self
    {
        $this->intro = $intro;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /*
 * Validation des datas de l'entity
 * -> réutilisé par le form pour controler les inputs
 */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint(
            'title',
            new Assert\Length([
                'min' => 10,
                'max' => 255,
                'minMessage' => 'Le title est trop court !'
            ])
        );

        $metadata->addPropertyConstraint(
            'image',
            new Assert\Url()
        );

        $metadata->addPropertyConstraint(
            'intro',
            new Assert\Length([
                'min' => 10,
                'max' => 255,
                'minMessage' => 'L\'intro est trop courte !'
            ])
        );

        $metadata->addPropertyConstraint(
            'content',
            new Assert\Length([
                'min' => 10,
                'max' => 255,
                'minMessage' => 'Le content est trop court !'
            ])
        );


        $metadata->addPropertyConstraint(
            'categories',
                new Assert\Count(['min'=>1]),
        );
/*
Marche pas sur le type
        $metadata->addPropertyConstraint(
            'categories',
             new Assert\Type(Category::class),
            //new Assert\Type(\ArrayCollection|Category::class),
            //new Assert\Type(\ArrayCollection::class),
        );
*/
    }

    /**
     * Call avant insertion
     * @ORM\PrePersist
     */
    public function prePersist():void{
        if(!$this->getCreatedAt()){
            $this->setCreatedAt(new \DateTime());
        }
        //$this->setUpdatedAt(new \DateTime());
    }
    /**
     * Call après insertion
     * @ORM\PostPersist
     */
    public function postPersist():void{

    }

    /**
     * Call avant Update
     * @ORM\PreUpdate
     */
    public function preUpdate():void{
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Call après Update
     * @ORM\PostUpdate
     */
    public function postUpdate():void{

    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function addCategories(Collection $categories): self{
        foreach($categories as $category){
            $this->addCategory($category);
        }

        return $this;
    }

    public function removeCategories(Collection $categories): self{
        foreach($categories as $category){
            $this->removeCategory($category);
        }

        return $this;
    }

}
