<?php
/**
 * Database connection + schema setup.
 * Requires $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME from config.php
 */

/* ---------- DATABASE ---------- */

try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS);

    if ($conn->connect_error) {
        die("MySQL Connection Failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");

    $conn->query("CREATE DATABASE IF NOT EXISTS `$DB_NAME`
                  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    $conn->select_db($DB_NAME);

    $conn->query("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_name VARCHAR(255) NOT NULL,
            product_code VARCHAR(100) UNIQUE,
            price DECIMAL(12,2) NOT NULL DEFAULT 0,
            stock INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS customers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_name VARCHAR(255) NOT NULL,
            mobile VARCHAR(30),
            address TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS bills (
            id INT AUTO_INCREMENT PRIMARY KEY,
            bill_no VARCHAR(60) NOT NULL UNIQUE,
            customer_id INT NULL,
            customer_name VARCHAR(255) NOT NULL DEFAULT 'Walk-in Customer',
            customer_mobile VARCHAR(30),
            bill_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
            discount DECIMAL(12,2) NOT NULL DEFAULT 0,
            grand_total DECIMAL(12,2) NOT NULL DEFAULT 0,
            payment_method ENUM('Cash','UPI','Credit') NOT NULL DEFAULT 'Cash',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS bill_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            bill_id INT NOT NULL,
            product_id INT NULL,
            product_name VARCHAR(255) NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            price DECIMAL(12,2) NOT NULL DEFAULT 0,
            amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            FOREIGN KEY (bill_id) REFERENCES bills(id) ON DELETE CASCADE
        ) ENGINE=InnoDB
    ");

    $check = $conn->query("SELECT id FROM users WHERE username='admin' LIMIT 1");
    if ($check && $check->num_rows === 0) {
        $hash = password_hash("admin123", PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users(username,password) VALUES(?,?)");
        $u = "admin";
        $stmt->bind_param("ss", $u, $hash);
        $stmt->execute();
        $stmt->close();
    }

} catch (Exception $e) {
    die("System Error: " . htmlspecialchars($e->getMessage()));
}

