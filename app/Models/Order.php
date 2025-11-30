<?php
// app/Models/Order.php

require_once __DIR__ . '/../../config/database.php';

class Order {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->createTablesIfNotExist();
    }

    private function createTablesIfNotExist() {
        // Create orders table if not exists
        $this->conn->exec("
            CREATE TABLE IF NOT EXISTS orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                order_number VARCHAR(50) UNIQUE NOT NULL,
                total_amount DECIMAL(15, 2) NOT NULL,
                status ENUM('pending', 'completed', 'cancelled') DEFAULT 'completed',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");

        // Create order_items table if not exists
        $this->conn->exec("
            CREATE TABLE IF NOT EXISTS order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT,
                product_name VARCHAR(255) NOT NULL,
                price DECIMAL(15, 2) NOT NULL,
                quantity INT NOT NULL,
                image VARCHAR(255),
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
            )
        ");
    }

    public function create($userId, $cartItems, $totalAmount) {
        $orderNumber = 'ORD' . time() . rand(100, 999);

        $stmt = $this->conn->prepare("INSERT INTO orders (user_id, order_number, total_amount, status) VALUES (?, ?, ?, 'completed')");
        $stmt->execute([$userId, $orderNumber, $totalAmount]);
        $orderId = $this->conn->lastInsertId();

        foreach ($cartItems as $item) {
            $stmt = $this->conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$orderId, $item['id'], $item['name'], $item['price'], $item['quantity'], $item['image']]);
        }

        return $orderNumber;
    }

    public function getUserOrders($userId) {
        $stmt = $this->conn->prepare("
            SELECT o.*, oi.product_name, oi.price, oi.quantity, oi.image
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.user_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getOrderDetails($orderId, $userId) {
        $stmt = $this->conn->prepare("
            SELECT o.*, oi.*
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.id = ? AND o.user_id = ?
        ");
        $stmt->execute([$orderId, $userId]);
        return $stmt->fetchAll();
    }
}
?>