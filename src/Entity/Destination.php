<?php

namespace App\Entity;

use App\Repository\DestinationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: DestinationRepository::class)]
class Destination
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The name field cannot be empty.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'The name cannot exceed {{ limit }} characters.'
    )]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'The description field cannot be empty.')]
    #[Assert\Length(
        min: 10,
        minMessage: 'The description must be at least {{ limit }} characters long.'
    )]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'The price field cannot be null.')]
    #[Assert\Positive(message: 'The price must be a positive number.')]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The duration field cannot be empty.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'The duration cannot exceed {{ limit }} characters.'
    )]
    private ?string $duration = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    public function toArray(string $baseUrl, string $uploadDir): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'image' => $this->image
                ? sprintf('%s/%s/%s', rtrim($baseUrl, '/'), trim($uploadDir, '/'), $this->image)
                : null,
            'price' => $this->getPrice(),
            'duration' => $this->getDuration(),
        ];
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }
}
