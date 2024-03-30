<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateTime = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?FamilyFolder $familyFolder = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $distributorCode = null;

    #[ORM\Column(nullable: true)]
    private ?float $distributorPrice = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\OneToMany(targetEntity: BudgetArticle::class, mappedBy: 'article', orphanRemoval: true)]
    private Collection $budgetArticle;

    public function __construct()
    {
        $this->budgetArticle = new ArrayCollection();
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

    public function getFamilyFolder(): ?FamilyFolder
    {
        return $this->familyFolder;
    }

    public function setFamilyFolder(?FamilyFolder $familyFolder): static
    {
        $this->familyFolder = $familyFolder;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getDistributorCode(): ?string
    {
        return $this->distributorCode;
    }

    public function setDistributorCode(?string $distributorCode): static
    {
        $this->distributorCode = $distributorCode;

        return $this;
    }

    public function getDistributorPrice(): ?float
    {
        return $this->distributorPrice;
    }

    public function setDistributorPrice(?float $distributorPrice): static
    {
        $this->distributorPrice = $distributorPrice;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, BudgetArticle>
     */
    public function getBudgetArticle(): Collection
    {
        return $this->budgetArticle;
    }

    public function addBudgetArticle(BudgetArticle $budgetArticle): static
    {
        if (!$this->budgetArticle->contains($budgetArticle)) {
            $this->budgetArticle->add($budgetArticle);
            $budgetArticle->setArticle($this);
        }

        return $this;
    }

    public function removeBudgetArticle(BudgetArticle $budgetArticle): static
    {
        if ($this->budgetArticle->removeElement($budgetArticle)) {
            // set the owning side to null (unless already changed)
            if ($budgetArticle->getArticle() === $this) {
                $budgetArticle->setArticle(null);
            }
        }

        return $this;
    }
}
