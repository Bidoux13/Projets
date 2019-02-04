<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 25,
     *      minMessage = "Votre pseudo doit faire au minimum {{ value }} caractères !",
     *      maxMessage = "Votre pseudo doit faire au maximum {{ value }} caractères !",
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(
     *      message = "L'email {{ value }} n'est pas valide !",
     *      checkMX = true
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     * @Assert\Length(
     *      max = 25,
     *      maxMessage = "Votre prénom doit avoir au maximum {{ value }} caractères !",
     * )
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     * @Assert\Length(
     *      max = 25,
     *      maxMessage = "Votre nom doit avoir au maximum {{ value }} caractères !",
     * )
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resetToken;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(array $list_roles, array $new_role): self
    {
        if ($new_role[0] == 'ROLE_ADMIN')
        {
            $list_roles[] = $new_role[0];
            $list_roles = array_unique($list_roles);
        }
        else
        {
            if (in_array('ROLE_ADMIN', $list_roles))
            {
                $list_roles = ['ROLE_ADMIN'];
                $list_roles[] = $new_role[0];
                $list_roles = array_unique($list_roles);
            }
            else
            {
                $list_roles = $new_role;
            }
        }

        $this->roles = $list_roles;
        return $this;
    }

    public function deleteRole(array $list_roles, array $role_to_del): self
    {
        $role_to_del = implode($role_to_del);
        if ($role_to_del == 'ROLE_ADMIN')
        {
            foreach ($list_roles as $role) {
                if ($role != $role_to_del) {
                    $list_roles_to_return[] = $role;
                    $list_roles_to_return = array_unique($list_roles_to_return);
                }
            }
        }
        else
        {
            if (in_array('ROLE_ADMIN', $list_roles))
            {
                $list_roles_to_return = ['ROLE_ADMIN', 'ROLE_USER'];
            }
            else
            {
                $list_roles_to_return = ['ROLE_USER'];
            }
        }

        $this->roles = $list_roles_to_return;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }
}
