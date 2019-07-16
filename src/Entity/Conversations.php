<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Conversations
 *
 * @ORM\Table(name="conversations", indexes={@ORM\Index(name="FK_conversations_users", columns={"sender"}), @ORM\Index(name="FK_conversations_users_2", columns={"reciver"})})
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
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sender", referencedColumnName="id")
     * })
     */
    private $sender;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reciver", referencedColumnName="id")
     * })
     */
    private $reciver;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?Users
    {
        return $this->sender;
    }

    public function setSender(?Users $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReciver(): ?Users
    {
        return $this->reciver;
    }

    public function setReciver(?Users $reciver): self
    {
        $this->reciver = $reciver;

        return $this;
    }


}
