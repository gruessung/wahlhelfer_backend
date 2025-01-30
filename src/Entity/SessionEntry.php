<?php

namespace App\Entity;

use App\Repository\SessionEntryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionEntryRepository::class)]
class SessionEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'entry_no')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Session $session = null;

    #[ORM\Column]
    private ?int $entry_no = null;

    #[ORM\Column]
    private ?int $page_no = null;

    #[ORM\Column]
    private ?bool $mark1 = null;

    #[ORM\Column]
    private ?bool $mark2 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;

        return $this;
    }

    public function getEntryNo(): ?int
    {
        return $this->entry_no;
    }

    public function setEntryNo(int $entry_no): static
    {
        $this->entry_no = $entry_no;

        return $this;
    }

    public function getPageNo(): ?int
    {
        return $this->page_no;
    }

    public function setPageNo(int $page_no): static
    {
        $this->page_no = $page_no;

        return $this;
    }

    public function isMark1(): ?bool
    {
        return $this->mark1;
    }

    public function setMark1(bool $mark1): static
    {
        $this->mark1 = $mark1;

        return $this;
    }

    public function isMark2(): ?bool
    {
        return $this->mark2;
    }

    public function setMark2(bool $mark2): static
    {
        $this->mark2 = $mark2;

        return $this;
    }

    public function asArray(): array {
        return [
          'entry_no' => $this->entry_no,
          'page_no' => $this->page_no,
          'mark1' => $this->mark1,
          'mark2' => $this->mark2
        ];
    }
}
