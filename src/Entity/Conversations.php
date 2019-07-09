<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Conversations
 *
 * @ORM\Table(name="conversations")
 * @ORM\Entity
 */
class Conversations
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user1", type="integer", nullable=false)
     */
    private $user1;

    /**
     * @var int
     *
     * @ORM\Column(name="user2", type="integer", nullable=false)
     */
    private $user2;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser1(): ?int
    {
        return $this->user1;
    }

    public function setUser1(int $user1): self
    {
        $this->user1 = $user1;

        return $this;
    }

    public function getUser2(): ?int
    {
        return $this->user2;
    }

    public function setUser2(int $user2): self
    {
        $this->user2 = $user2;

        return $this;
    }


}
