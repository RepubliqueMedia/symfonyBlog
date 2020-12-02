<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * add pre/postpersist
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /*
     * Used for confirm
     */
    private $confirm_password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getConfirmPassword(): string
    {
        return (string) $this->confirm_password;
    }

    public function setConfirmPassword(string $password): self
    {
        $this->config_password = $password;

        return $this;
    }
    /**
     * Call avant Update
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preUpdate():void{
        //marche pas
        //$passwordEncoder=new UserPasswordEncoderInterface();
        //$this->password=$passwordEncoder->encodePassword($this,$this->password);
        //$this->setUpdatedAt(new \DateTime());
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /*
    * Validation des datas de l'entity
    * -> réutilisé par le form pour controler les inputs
    */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint(
            'email',
            new Assert\Email()
        );

        $metadata->addPropertyConstraint(
            'password',
            new Assert\Length([
                    'min' => 8,
                    'max' => 20,
                    'minMessage' => 'Votre mot de passe est trop court !',
                    'maxMessage' => 'Votre mot de passe est trop long !',
                ])
        );

        $metadata->addPropertyConstraint(
            'password',
            new Assert\EqualTo([
                'propertyPath' => 'confirm_password',
                'message' => 'Les mots de passe ne sont pas identique !'
            ])
        );

    }
}
