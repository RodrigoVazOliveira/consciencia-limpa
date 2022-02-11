<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category implements \JsonSerializable {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function setId($id): void {
        $this->id = $id;
    }

    public function __serialize(): array {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }

    public function __toString(): string {
        return json_encode($this);
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }

}
