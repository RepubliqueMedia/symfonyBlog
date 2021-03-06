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

//Apparemment auto load
//use App\Entity\AbstractEntity as AbstractEntity;
/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Article extends AbstractEntity2
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * #Auto\Get
     */
    //protected $id;
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     * #Auto\Public
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255)
     * #Auto\Public
     */
    protected $image;

    /**
     * @ORM\Column(type="string", length=255)
     * #Auto\Public
     */
    protected $intro;

    /**
     * @ORM\Column(type="text")
     * #Auto\Public
     */
    protected $content;

    /**
     * @ORM\Column(type="datetime")
     * #Auto\Public
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     * #Auto\Public
     */
    protected $updatedAt;


    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="articles")
     * #Auto\Get
     */
    protected $categories;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="article", orphanRemoval=true)
     * #Auto\Get
     */
    protected $comments;


    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->comments = new ArrayCollection();
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

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

}
