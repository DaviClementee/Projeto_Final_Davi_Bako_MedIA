<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/Database.php';

class EmailVerificationDAO {

    public function createForUser($userId, $ttlSeconds = 300) {
        $pdo = DatabaseSingle::connect();
        $token     = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        $pdo->beginTransaction();
        try {
            $sql = "
                INSERT INTO email_verifications (user_id, token_hash, expires_at, used_at, created_at)
                VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? SECOND), NULL, NOW())
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId, $tokenHash, $ttlSeconds]);
            $pdo->commit();
            return $token;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function validateToken($token) {
        $pdo = DatabaseSingle::connect();
        $tokenHash = hash('sha256', $token);

        $sql = "
            SELECT user_id
            FROM email_verifications
            WHERE token_hash = ?
              AND used_at IS NULL
              AND expires_at > NOW()
            ORDER BY id DESC
            LIMIT 1
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tokenHash]);
        $userId = $stmt->fetchColumn();

        return $userId ? (int)$userId : false;
    }

    public function markUsed(string $token): void {
        $pdo = DatabaseSingle::connect();
        $tokenHash = hash('sha256', $token);

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("
                UPDATE email_verifications
                SET used_at = NOW()
                WHERE token_hash = ?
            ");
            $stmt->execute([$tokenHash]);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
