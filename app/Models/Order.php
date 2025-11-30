<?php

require_once __DIR__ . '/../../config/database.php';

class Order {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->createTablesIfNotExist();
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