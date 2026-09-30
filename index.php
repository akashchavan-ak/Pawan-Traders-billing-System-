<?php
/*
=========================================================
 PAWAN TRADERS - BILLING SYSTEM
 PHP + MySQL + HTML + CSS
 GST FREE
=========================================================
Default Login:
Username: admin
Password: admin123
=========================================================
*/

session_start();

require __DIR__ . '/config.php';
require __DIR__ . '/db.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';

require __DIR__ . '/actions/products.php';
require __DIR__ . '/actions/customers.php';
require __DIR__ . '/actions/billing.php';

/* ---------- ROUTE ---------- */

$page = $_GET['page'] ?? "dashboard";

if ($page === "print_bill") {
    require __DIR__ . '/pages/print_bill.php';
    exit;
}

/* ---------- EDIT FORMS ---------- */

$editCustomer = null;
if ($page === "customers" && isset($_GET['edit_customer'])) {
    $id = (int)$_GET['edit_customer'];
    $stmt = $conn->prepare("SELECT * FROM customers WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editCustomer = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$editProduct = null;
if ($page === "products" && isset($_GET['edit_product'])) {
    $id = (int)$_GET['edit_product'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editProduct = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

/* ---------- BILLING CUSTOMER SELECTION ---------- */

$selectedCustomer = null;
if ($page === "billing" && isset($_GET['customer_id'])) {
    $cid = (int)$_GET['customer_id'];
    $stmt = $conn->prepare("SELECT * FROM customers WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $cid);
    $stmt->execute();
    $selectedCustomer = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

/* ---------- LAYOUT ---------- */

require __DIR__ . '/includes/header.php';

switch ($page) {
    case 'dashboard':
        require __DIR__ . '/pages/dashboard.php';
        break;
    case 'customers':
        require __DIR__ . '/pages/customers.php';
        break;
    case 'products':
        require __DIR__ . '/pages/products.php';
        break;
    case 'billing':
        require __DIR__ . '/pages/billing.php';
        break;
    case 'sales':
        require __DIR__ . '/pages/sales.php';
        break;
    default:
        require __DIR__ . '/pages/404.php';
}

require __DIR__ . '/includes/footer.php';
