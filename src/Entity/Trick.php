<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TrickGroup $trickGroup = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $firstImage = null;

    #[ORM\Column(length: 255)]
    private ?string $image1 = null;

    #[ORM\Column(length: 255)]
    private ?string $image2 = null;

    #[ORM\Column(length: 255)]
    private ?string $image3 = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $media1 = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $media2 = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $media3 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTrickGroup(): ?TrickGroup
    {
        return $this->trickGroup;
    }

    public function setTrickGroup(?TrickGroup $trickGroup): self
    {
        $this->trickGroup = $trickGroup;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getFirstImage(): ?string
    {
        return $this->firstImage;
    }

    public function setFirstImage(string $firstImage): self
    {
        $this->firstImage = $firstImage;

        return $this;
    }

    public function getImage1(): ?string
    {
        return $this->image1;
    }

    public function setImage1(string $image1): self
    {
        $this->image1 = $image1;

        return $this;
    }

    public function getImage2(): ?string
    {
        return $this->image2;
    }

    public function setImage2(string $image2): self
    {
        $this->image2 = $image2;

        return $this;
    }

    public function getImage3(): ?string
    {
        return $this->image3;
    }

    public function setImage3(string $image3): self
    {
        $this->image3 = $image3;

        return $this;
    }

    public function getMedia1(): ?string
    {
        return $this->media1;
    }

    public function setMedia1(string $media1): self
    {
        $this->media1 = $media1;

        return $this;
    }

    public function getMedia2(): ?string
    {
        return $this->media2;
    }

    public function setMedia2(string $media2): self
    {
        $this->media2 = $media2;

        return $this;
    }

    public function getMedia3(): ?string
    {
        return $this->media3;
    }

    public function setMedia3(string $media3): self
    {
        $this->media3 = $media3;

        return $this;
    }
}
