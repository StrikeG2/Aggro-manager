<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prixUnitaire = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userR = null;

    /**
     * @var Collection<int, MouvementProduit>
     */
    #[ORM\OneToMany(targetEntity: MouvementProduit::class, mappedBy: 'produit')]
    private Collection $mouvementProduits;

    public function __construct()
    {
        $this->mouvementProduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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

    public function getPrixUnitaire(): ?string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(string $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
    public function getUserR(): ?User
    {
        return $this->userR;
    }

    public function setUserR(?User $userR): static
    {
        $this->userR = $userR;

        return $this;
    }

    /**
     * @return Collection<int, MouvementProduit>
     */
    public function getMouvementProduits(): Collection
    {
        return $this->mouvementProduits;
    }

    public function addMouvementProduit(MouvementProduit $mouvementProduit): static
    {
        if (!$this->mouvementProduits->contains($mouvementProduit)) {
            $this->mouvementProduits->add($mouvementProduit);
            $mouvementProduit->setProduit($this);
        }

        return $this;
    }

    public function removeMouvementProduit(MouvementProduit $mouvementProduit): static
    {
        if ($this->mouvementProduits->removeElement($mouvementProduit)) {
            // set the owning side to null (unless already changed)
            if ($mouvementProduit->getProduit() === $this) {
                $mouvementProduit->setProduit(null);
            }
        }

        return $this;
    }
}
