<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="messages", indexes={@ORM\Index(name="FK_conversations_messages", columns={"conv_id"}), @ORM\Index(name="FK_users_messages", columns={"sender", "recipient"})})
 * @ORM\Entity(repositoryClass="App\Repository\MessagesRepository")
 */
class Messages
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \Conversations
     *
     * @ORM\ManyToOne(targetEntity="Conversations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="conv_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $conv_id;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sender", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $sender;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="recipient", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $recipient;

    /**
     * @ORM\Column(type="datetime")
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $content;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConvId(): ?Conversations
    {
        return $this->conv_id;
    }

    public function setConvId(?Conversations $conv_id): self
    {
        $this->conv_id = $conv_id;

        return $this;
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

    public function getRecipient(): ?Users
    {
        return $this->recipient;
    }

    public function setRecipient(?Users $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;

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
}
