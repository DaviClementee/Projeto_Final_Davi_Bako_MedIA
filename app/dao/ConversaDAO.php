<?php

require_once __DIR__ . '/../models/Conversa.php';
require_once __DIR__ . '/../config/Database.php';

class ConversaDAO {

    public function create(int $userId, string $titulo = 'Nova Conversa'): int {
        $pdo = DatabaseSingle::connect();
        $pdo->beginTransaction();
        try {
            $sql = "
                INSERT INTO conversas (user_id, titulo, created_at, updated_at)
                VALUES (?, ?, NOW(), NOW())
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId, $titulo]);
            $id = (int)$pdo->lastInsertId();
            $pdo->commit();
            return $id;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function findById(int $id): ?Conversa {
        $pdo = DatabaseSingle::connect();
        $sql = "SELECT * FROM conversas WHERE id = ? AND deleted_at IS NULL LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Conversa(
                $row['id'],
                $row['user_id'],
                $row['titulo'],
                $row['created_at'],
                $row['updated_at'],
                $row['deleted_at']
            );
        }
        return null;
    }

    public function findByUserId(int $userId): array {
        $pdo = DatabaseSingle::connect();
        $sql = "
            SELECT * FROM conversas
            WHERE user_id = ? AND deleted_at IS NULL
            ORDER BY updated_at DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $conversas = [];
        foreach ($rows as $row) {
            $conversas[] = new Conversa(
                $row['id'],
                $row['user_id'],
                $row['titulo'],
                $row['created_at'],
                $row['updated_at'],
                $row['deleted_at']
            );
        }
        return $conversas;
    }

    public function updateTitulo(int $id, string $titulo): bool {
        $pdo = DatabaseSingle::connect();
        $pdo->beginTransaction();
        try {
            $sql = "UPDATE conversas SET titulo = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([$titulo, $id]);
            $pdo->commit();
            return $result;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool {
        $pdo = DatabaseSingle::connect();
        $pdo->beginTransaction();
        try {
            $sql = "UPDATE conversas SET deleted_at = NOW() WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([$id]);
            $pdo->commit();
            return $result;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function belongsToUser(int $conversaId, int $userId): bool {
        $pdo = DatabaseSingle::connect();
        $sql = "SELECT id FROM conversas WHERE id = ? AND user_id = ? AND deleted_at IS NULL LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$conversaId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
}
