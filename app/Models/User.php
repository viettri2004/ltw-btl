<?php

require_once __DIR__ . '/../../config/database.php';

class User {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->conn->prepare("INSERT INTO users (full_name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['full_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['role'] ?? 'member'
        ]);
    }

    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT id, full_name, email, phone, address, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
        return $stmt->execute([
            $data['full_name'],
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $id
        ]);
    }
}
?>