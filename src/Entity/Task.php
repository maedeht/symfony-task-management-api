<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    const TODO = 1;
    const DOING = 2;
    const DONE = 3;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start_time;

    /**
     * @ORM\Column(type="integer", options={"default":1})
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tasks", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    public function __construct()
    {
        $this->created_at = new DateTime();
        $this->updated_at = new DateTime();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setAssignedAt(\DateTimeInterface $assigned_at): self
    {
        $this->assigned_at = $assigned_at;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getStartTime(): ?string
    {
        return $this->start_time->format('Y-m-d H:i:s');
    }

    public function setStartTime(string $start_time): self
    {
        $this->start_time = new DateTime($start_time);

        return $this;
    }

    public function getStatus(): ?string
    {
        $statusTitle = [
            self::TODO => 'TODO',
            self::DOING => 'DOING',
            self::DONE => 'DONE'
        ];
        return $statusTitle[$this->status];
    }

    public function setStatus(?string $status): self
    {
        $int_status = [
            'TODO' => self::TODO,
            'DOING' => self::DOING,
            'DONE' => self::DONE
        ];
        $this->status = $int_status[strtoupper($status)];

        return $this;
    }

    public function getUser(): ?object
    {
        return (object)[
            'id' => $this->user->getId(),
            'email' => $this->user->getEmail(),
            'roles' => $this->user->getRoles()
        ];
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at->format('Y-m-d H:i:s');
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

}
