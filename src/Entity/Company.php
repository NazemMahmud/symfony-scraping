<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;



#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[Index(columns: ["regi_code"], name: "regi_code_idx")]
#[ORM\HasLifecycleCallbacks]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 40,  unique: true)]
    private ?string $regi_code = null;

    #[ORM\Column(length: 40)]
    private ?string $vat = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $mobile_phone = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $created_at;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updated_at;

    #[ORM\Column(type: 'datetime' , nullable: true)]
    private ?\DateTimeInterface $deleted_at = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRegiCode(): ?string
    {
        return $this->regi_code;
    }

    public function setRegiCode(string $regi_code): static
    {
        $this->regi_code = $regi_code;

        return $this;
    }

    public function getVat(): ?string
    {
        return $this->vat;
    }

    public function setVat(string $vat): static
    {
        $this->vat = $vat;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getMobilePhone(): ?string
    {
        return $this->mobile_phone;
    }

    public function setMobilePhone(?string $mobile_phone): static
    {
        $this->mobile_phone = $mobile_phone;

        return $this;
    }


    public function getCreatedAt(): \DateTimeInterface|null
    {
        return $this->created_at;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): static
    {
        $this->created_at = new \DateTime();

        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updated_at;
    }


    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): static
    {
        $this->updated_at = new \DateTime();
        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setSoftDelete(?\DateTimeInterface $deleted_at ): static
    {
        $this->deleted_at = $deleted_at ;

        return $this;
    }
}
