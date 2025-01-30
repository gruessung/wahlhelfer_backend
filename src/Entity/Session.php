<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, SessionEntry>
     */
    #[ORM\OneToMany(targetEntity: SessionEntry::class, mappedBy: 'session', orphanRemoval: true)]
    private Collection $sessionEntries;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    public function __construct()
    {
        $this->sessionEntries = new ArrayCollection();
    }

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

    public function asArray(): array {
        return [
          'id' => $this->id,
          'session_uuid' => $this->session_uuid,
          'name' => $this->name,
          'cnt_pages' => $this->cnt_pages,
          'cnt_entries_per_page' => $this->cnt_entries_per_page,
          'cnt_entries_total' => $this->cnt_entries_total,
            'entries' => $this->sessionEntries->map(
                function (SessionEntry $entry) {
                    return $entry->asArray();
                }
            )->getValues(),
            'diff' => [
                'total' => array_sum($this->sessionEntries->map(
                    function (SessionEntry $entry) {
                        return ($entry->isMark1() == $entry->isMark2()) ? 0 : 1;
                    }
                )->getValues()),
                'mark2_no_mark1' => array_sum($this->sessionEntries->map(
                    function (SessionEntry $entry) {
                        return (!$entry->isMark1() && $entry->isMark2()) ? 1 : 0;
                    }
                )->getValues())
            ],
            'summary' => [
                'total_entries' => $this->cnt_entries_total,
                'mark1_no_mark2' => array_sum($this->sessionEntries->map(
                    function (SessionEntry $entry) {
                        return ($entry->isMark1() && !$entry->isMark2()) ? 1 : 0;
                    }
                )->getValues()),
                'finished' => array_sum($this->sessionEntries->map(
                    function (SessionEntry $entry) {
                        return ($entry->isMark1() && $entry->isMark2()) ? 1 : 0;
                    }
                )->getValues())
            ]
        ];
    }

    /**
     * @return Collection<int, SessionEntry>
     */
    public function getEntryNo(): Collection
    {
        return $this->sessionEntries;
    }

    public function addSessionEntry(SessionEntry $entry): static
    {
        if (!$this->sessionEntries->contains($entry)) {
            $this->sessionEntries->add($entry);
            $entry->setSession($this);
        }

        return $this;
    }

    public function removeSessionEntry(SessionEntry $entry): static
    {
        if ($this->sessionEntries->removeElement($entry)) {
            // set the owning side to null (unless already changed)
            if ($entry->getSession() === $this) {
                $entry->setSession(null);
            }
        }

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }
}
