<?php

class Conversa {
    private int $id;
    private int $userId;
    private string $titulo;
    private ?string $createdAt;
    private ?string $updatedAt;
    private ?string $deletedAt;

    public function __construct(
        int $id = 0,
        int $userId = 0,
        string $titulo = 'Nova Conversa',
        ?string $createdAt = null,
        ?string $updatedAt = null,
        ?string $deletedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->titulo = $titulo;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->deletedAt = $deletedAt;
    }

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getUserId(): int {
        return $this->userId;
    }

    public function setUserId(int $userId): void {
        $this->userId = $userId;
    }

    public function getTitulo(): string {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): void {
        $this->titulo = $titulo;
    }

    public function getCreatedAt(): ?string {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?string {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?string $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }

    public function getDeletedAt(): ?string {
        return $this->deletedAt;
    }

    public function setDeletedAt(?string $deletedAt): void {
        $this->deletedAt = $deletedAt;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'titulo' => $this->titulo,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}