<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[UniqueEntity('reference', "Cet reference est déja pris")]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    

    #[ORM\OneToMany(mappedBy: 'id_commande', targetEntity: LigneCommande::class)]
    private Collection $ligneCommandes;

    // #[ORM\ManyToOne(inversedBy: 'commandes')]
    // #[ORM\JoinColumn(nullable: false)]
    // private ?user $id_user ;
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?float $prixAvecTva = null;

    #[ORM\Column]
    private ?int $state = null;

    public function __construct()
    {
        $this->ligneCommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    

    /**
     * @return Collection<int, LigneCommande>
     */
    public function getLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

    public function addLigneCommande(LigneCommande $ligneCommande): self
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes->add($ligneCommande);
            $ligneCommande->setIdCommande($this);
        }

        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): self
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            // set the owning side to null (unless already changed)
            if ($ligneCommande->getIdCommande() === $this) {
                $ligneCommande->setIdCommande(null);
            }
        }

        return $this;
    }

    // public function getIdUser(): ?user
    // {
    //     return $this->id_user;
    // }

    // public function setIdUser(?user $id_user): self
    // {
    //     $this->id_user = $id_user;

    //     return $this;
    // }
    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $id_user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdeAt = $createdAt;

        return $this;
    }

    public function getPrixAvecTva(): ?float
    {
        return $this->prixAvecTva;
    }

    public function setPrixAvecTva(float $prixAvecTva): self
    {
        $this->prixAvecTva = $prixAvecTva;

        return $this;
    }
    public function getTotal():float
    {
        $total = 0;
        foreach ($this->getLigneCommandes() as $product) {
            $total += $product->getQuantite() * $product->getProduit()->getPU();
        }
        return $total;
  
    }
    
    

    public function getTotalQuantity():float
    {
        $total = 0;
        foreach ($this->getLigneCommandes() as $product) {
            $total += $product->getQuantite();
        }
        return $total;
  
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }

}
