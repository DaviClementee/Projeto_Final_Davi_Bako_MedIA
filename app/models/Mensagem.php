<?php

class Mensagem {
    private int $id;
    private int $conversaId;
    private string $role;
    private string $conteudo;
    private ?string $createdAt;

    public function __construct(
        int $id = 0,
        int $conversaId = 0,
        string $role = 'user',
        string $conteudo = '',
        ?string $createdAt = null
    ) {
        $this->id = $id;
        $this->conversaId = $conversaId;
        $this->role = $role;
        $this->conteudo = $conteudo;
        $this->createdAt = $createdAt;
    }

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getConversaId(): int {
        return $this->conversaId;
    }

    public function setConversaId(int $conversaId): void {
        $this->conversaId = $conversaId;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function setRole(string $role): void {
        $this->role = $role;
    }

    public function getConteudo(): string {
        return $this->conteudo;
    }

    public function setConteudo(string $conteudo): void {
        $this->conteudo = $conteudo;
    }

    public function getCreatedAt(): ?string {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'conversa_id' => $this->conversaId,
            'role' => $this->role,
            'conteudo' => $this->conteudo,
            'created_at' => $this->createdAt,
        ];
    }
}