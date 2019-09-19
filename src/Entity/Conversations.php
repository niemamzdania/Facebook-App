<?php

namespace App\Entity;
use App\Entity\Users;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="conversations", indexes={@ORM\Index(name="FK_users_conversations", columns={"user_1", "user_2"})})
 * @ORM\Entity(repositoryClass="App\Repository\ConversationsRepository")
 */
class Conversations
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_1", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $user_1;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_2", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $user_2;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser1(): ?Users
    {
        return $this->user_1;
    }

    public function setUser1(?Users $user_1): self
    {
        $this->user_1 = $user_1;

        return $this;
    }

    public function getUser2(): ?Users
    {
        return $this->user_2;
    }

    public function setUser2(?Users $user_2): self
    {
        $this->user_2 = $user_2;

        return $this;
    }
}
