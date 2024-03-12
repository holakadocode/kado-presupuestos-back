<?php

namespace App\Entity;

use App\Repository\BudgetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BudgetRepository::class)]
class Budget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateTime = null;

    #[ORM\ManyToOne(inversedBy: 'budgets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $clientTaxIdentification = null;

    #[ORM\Column(length: 255)]
    private ?string $clientName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $clientEmail = null;

    #[ORM\Column(nullable: true)]
    private ?int $clientTlf = null;

    #[ORM\Column(length: 255)]
    private ?string $budgetID = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?float $subTotal = null;

    #[ORM\Column(nullable: true)]
    private ?float $iva = null;

    #[ORM\Column(nullable: true)]
    private ?float $total = null;

    #[ORM\Column(nullable: true)]
    private ?array $clientAddress = null;

    #[ORM\OneToMany(targetEntity: BudgetArticle::class, mappedBy: 'budget', orphanRemoval: true)]
    private Collection $budgetArticles;

    public function __construct()
    {
        $this->budgetArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime): static
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getClientTaxIdentification(): ?string
    {
        return $this->clientTaxIdentification;
    }

    public function setClientTaxIdentification(?string $clientTaxIdentification): static
    {
        $this->clientTaxIdentification = $clientTaxIdentification;

        return $this;
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(string $clientName): static
    {
        $this->clientName = $clientName;

        return $this;
    }

    public function getClientEmail(): ?string
    {
        return $this->clientEmail;
    }

    public function setClientEmail(?string $clientEmail): static
    {
        $this->clientEmail = $clientEmail;

        return $this;
    }

    public function getClientTlf(): ?int
    {
        return $this->clientTlf;
    }

    public function setClientTlf(?int $clientTlf): static
    {
        $this->clientTlf = $clientTlf;

        return $this;
    }

    public function getBudgetID(): ?string
    {
        return $this->budgetID;
    }

    public function setBudgetID(string $budgetID): static
    {
        $this->budgetID = $budgetID;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSubTotal(): ?float
    {
        return $this->subTotal;
    }

    public function setSubTotal(?float $subTotal): static
    {
        $this->subTotal = $subTotal;

        return $this;
    }

    public function getIva(): ?float
    {
        return $this->iva;
    }

    public function setIva(?float $iva): static
    {
        $this->iva = $iva;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(?float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getClientAddress(): ?array
    {
        return $this->clientAddress;
    }

    public function setClientAddress(?array $clientAddress): static
    {
        $this->clientAddress = $clientAddress;

        return $this;
    }

    /**
     * @return Collection<int, BudgetArticle>
     */
    public function getBudgetArticles(): Collection
    {
        return $this->budgetArticles;
    }

    public function addBudgetArticle(BudgetArticle $budgetArticle): static
    {
        if (!$this->budgetArticles->contains($budgetArticle)) {
            $this->budgetArticles->add($budgetArticle);
            $budgetArticle->setBudget($this);
        }

        return $this;
    }

    public function removeBudgetArticle(BudgetArticle $budgetArticle): static
    {
        if ($this->budgetArticles->removeElement($budgetArticle)) {
            // set the owning side to null (unless already changed)
            if ($budgetArticle->getBudget() === $this) {
                $budgetArticle->setBudget(null);
            }
        }

        return $this;
    }
}
