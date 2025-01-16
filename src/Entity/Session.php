<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $session_uuid = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $cnt_pages = null;

    #[ORM\Column]
    private ?int $cnt_entries_per_page = null;

    #[ORM\Column]
    private ?int $cnt_entries_total = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionUuid(): ?string
    {
        return $this->session_uuid;
    }

    public function setSessionUuid(string $session_uuid): static
    {
        $this->session_uuid = $session_uuid;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCntPages(): ?int
    {
        return $this->cnt_pages;
    }

    public function setCntPages(int $cnt_pages): static
    {
        $this->cnt_pages = $cnt_pages;

        return $this;
    }

    public function getCntEntriesPerPage(): ?int
    {
        return $this->cnt_entries_per_page;
    }

    public function setCntEntriesPerPage(int $cnt_entries_per_page): static
    {
        $this->cnt_entries_per_page = $cnt_entries_per_page;

        return $this;
    }

    public function getCntEntriesTotal(): ?int
    {
        return $this->cnt_entries_total;
    }

    public function setCntEntriesTotal(int $cnt_entries_total): static
    {
        $this->cnt_entries_total = $cnt_entries_total;

        return $this;
    }
}
