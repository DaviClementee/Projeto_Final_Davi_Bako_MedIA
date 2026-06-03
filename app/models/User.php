<?php

class User {

  private int $id;

  private string $username;

  private string $email;

  private string $password;

  private bool $isAdmin;

  private ?string $createdAt;

  private ?string $updatedAt;

  private ?string $deletedAt;
 
  public function __construct(

    int $id = 0,

    string $username = '',

    string $email = '',

    string $password = '',

    bool $isAdmin = false,

    ?string $createdAt = null,

    ?string $updatedAt = null,

    ?string $deletedAt = null

  ) {

    $this->id = $id;

    $this->username = $username;

    $this->email = $email;

    $this->password = $password;

    $this->isAdmin = $isAdmin;

    $this->createdAt = $createdAt;

    $this->updatedAt = $updatedAt;

    $this->deletedAt = $deletedAt;

  }
 
  public function getId(): int

  {

    return $this->id;

  }

  public function setId(int $id): void

  {

    $this->id = $id;

  }
 
  public function getUsername(): string

  {

    return $this->username;

  }

  public function setUsername(string $username): void

  {

    $this->username = $username;

  }
 
  public function getEmail(): string

  {

    return $this->email;

  }

  public function setEmail(string $email): void

  {

    $this->email = $email;

  }
 
  public function getPassword(): string

  {

    return $this->password;

  }

  public function setPassword(string $password): void

  {

    $this->password = $password;

  }
 
  public function isAdmin(): bool

  {

    return $this->isAdmin;

  }

  public function setIsAdmin(bool $isAdmin): void

  {

    $this->isAdmin = $isAdmin;

  }
 
  public function getCreatedAt(): ?string

  {

    return $this->createdAt;

  }

  public function setCreatedAt(?string $createdAt): void

  {

    $this->createdAt = $createdAt;

  }
 
  public function getUpdatedAt(): ?string

  {

    return $this->updatedAt;

  }

  public function setUpdatedAt(?string $updatedAt): void

  {

    $this->updatedAt = $updatedAt;

  }
 
  public function getDeletedAt(): ?string

  {

    return $this->deletedAt;

  }

  public function setDeletedAt(?string $deletedAt): void

  {

    $this->deletedAt = $deletedAt;

  }

}
 