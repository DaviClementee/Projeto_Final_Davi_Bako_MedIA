<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/Database.php';

class UserDAO {

    public function findById($userId) {
        $pdo = DatabaseSingle::connect();
        $sql = "
            SELECT *
            FROM users
            WHERE id = :id
            AND is_verified = 1
            AND verified_at IS NOT NULL
            LIMIT 1
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new User(
                $row['id'],
                $row['username'],
                $row['email'],
                $row['password'],
                (bool)$row['is_admin'],
            );
        }
        return false;
    }

    public function findByEmail($email) {
        $pdo = DatabaseSingle::connect();
        $sql = "
            SELECT *
            FROM users
            WHERE email = :email
            AND is_verified = 1
            AND verified_at IS NOT NULL
            LIMIT 1
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new User(
                $row['id'],
                $row['username'],
                $row['email'],
                $row['password'],
                (bool)$row['is_admin'],
            );
        }
        return false;
    }

    public function findByEmailAny($email) {
        $pdo = DatabaseSingle::connect();
        $sql = "SELECT id FROM users WHERE email = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createPending($username, $email) {
        $pdo = DatabaseSingle::connect();
        $pdo->beginTransaction();
        try {
            $sql = "
                INSERT INTO users (username, email, password, is_admin, is_verified, verified_at, created_at, updated_at, deleted_at)
                VALUES (?, ?, '', 0, 0, NULL, NOW(), NOW(), NULL)
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username, $email]);
            $id = (int)$pdo->lastInsertId();
            $pdo->commit();
            return $id;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function setPasswordAndVerify(int $userId, string $passwordHash): void {
        $pdo = DatabaseSingle::connect();
        $pdo->beginTransaction();
        try {
            $sql = "
                UPDATE users
                SET password = ?,
                    is_verified = 1,
                    verified_at = NOW(),
                    updated_at = NOW()
                WHERE id = ?
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$passwordHash, $userId]);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function createUser($username, $email, $passwordHash): int {
        $pdo = DatabaseSingle::connect();
        $pdo->beginTransaction();
        try {
            $sql = "
                INSERT INTO users (username, email, password, is_admin, is_verified, verified_at, created_at, updated_at, deleted_at)
                VALUES (?, ?, ?, 0, 1, NOW(), NOW(), NOW(), NULL)
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username, $email, $passwordHash]);
            $id = (int)$pdo->lastInsertId();
            $pdo->commit();
            return $id;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
