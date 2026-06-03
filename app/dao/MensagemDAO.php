<?php

require_once __DIR__ . '/../models/Mensagem.php';
require_once __DIR__ . '/../config/Database.php';

class MensagemDAO {

    public function create(int $conversaId, string $role, string $conteudo): int {
        $pdo = DatabaseSingle::connect();
        $pdo->beginTransaction();
        try {
            $sql = "
                INSERT INTO mensagens (conversa_id, role, conteudo, created_at)
                VALUES (?, ?, ?, NOW())
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$conversaId, $role, $conteudo]);
            $id = (int)$pdo->lastInsertId();

            // Atualiza timestamp da conversa dentro da mesma transação
            $pdo->prepare("UPDATE conversas SET updated_at = NOW() WHERE id = ?")
                ->execute([$conversaId]);

            $pdo->commit();
            return $id;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function findById(int $id): ?Mensagem {
        $pdo = DatabaseSingle::connect();
        $sql = "SELECT * FROM mensagens WHERE id = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Mensagem(
                $row['id'],
                $row['conversa_id'],
                $row['role'],
                $row['conteudo'],
                $row['created_at']
            );
        }
        return null;
    }

    public function findByConversaId(int $conversaId): array {
        $pdo = DatabaseSingle::connect();
        $sql = "SELECT * FROM mensagens WHERE conversa_id = ? ORDER BY created_at ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$conversaId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $mensagens = [];
        foreach ($rows as $row) {
            $mensagens[] = new Mensagem(
                $row['id'],
                $row['conversa_id'],
                $row['role'],
                $row['conteudo'],
                $row['created_at']
            );
        }
        return $mensagens;
    }

    public function getLastMessage(int $conversaId): ?Mensagem {
        $pdo = DatabaseSingle::connect();
        $sql = "SELECT * FROM mensagens WHERE conversa_id = ? ORDER BY created_at DESC LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$conversaId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Mensagem(
                $row['id'],
                $row['conversa_id'],
                $row['role'],
                $row['conteudo'],
                $row['created_at']
            );
        }
        return null;
    }

    public function countByConversaId(int $conversaId): int {
        $pdo = DatabaseSingle::connect();
        $sql = "SELECT COUNT(*) as total FROM mensagens WHERE conversa_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$conversaId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }
}
