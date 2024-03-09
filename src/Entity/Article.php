<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
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

    #[ORM\OneToOne(inversedBy: 'article', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?BudgetArticle $budgetArticle = null;

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

    public function getBudgetArticle(): ?BudgetArticle
    {
        return $this->budgetArticle;
    }

    public function setBudgetArticle(BudgetArticle $budgetArticle): static
    {
        $this->budgetArticle = $budgetArticle;

        return $this;
    }
}
