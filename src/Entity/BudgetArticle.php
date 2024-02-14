<?php

namespace App\Entity;

use App\Repository\BudgetArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BudgetArticleRepository::class)]
class BudgetArticle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateTime = null;

    #[ORM\Column(length: 255)]
    private ?string $nameArticle = null;

    #[ORM\Column]
    private ?int $total = null;

    #[ORM\OneToMany(targetEntity: Budget::class, mappedBy: 'budgetArticle')]
    private Collection $budget;

    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'budgetArticle')]
    private Collection $articles;

    public function __construct()
    {
        $this->budget = new ArrayCollection();
        $this->articles = new ArrayCollection();
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

    public function getNameArticle(): ?string
    {
        return $this->nameArticle;
    }

    public function setNameArticle(string $nameArticle): static
    {
        $this->nameArticle = $nameArticle;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): static
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection<int, Budget>
     */
    public function getBudget(): Collection
    {
        return $this->budget;
    }

    public function addBudget(Budget $budget): static
    {
        if (!$this->budget->contains($budget)) {
            $this->budget->add($budget);
            $budget->setBudgetArticle($this);
        }

        return $this;
    }

    public function removeBudget(Budget $budget): static
    {
        if ($this->budget->removeElement($budget)) {
            // set the owning side to null (unless already changed)
            if ($budget->getBudgetArticle() === $this) {
                $budget->setBudgetArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setBudgetArticle($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getBudgetArticle() === $this) {
                $article->setBudgetArticle(null);
            }
        }

        return $this;
    }
}
