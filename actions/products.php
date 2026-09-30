<?php
/* ---------- PRODUCT ACTIONS ---------- */

if (isset($_POST['add_product'])) {
    $name = trim($_POST['product_name'] ?? "");
    $code = trim($_POST['product_code'] ?? "");
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);

    if ($name !== "") {
        $stmt = $conn->prepare("INSERT INTO products(product_name,product_code,price,stock) VALUES(?,?,?,?)");
        $stmt->bind_param("ssdi",$name,$code,$price,$stock);
        $stmt->execute();
        $stmt->close();
    }
    go("index.php?page=products");
}

if (isset($_POST['update_product'])) {
    $id = (int)($_POST['product_id'] ?? 0);
    $name = trim($_POST['product_name'] ?? "");
    $code = trim($_POST['product_code'] ?? "");
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);

    if ($id > 0 && $name !== "") {
        $stmt = $conn->prepare("UPDATE products SET product_name=?,product_code=?,price=?,stock=? WHERE id=?");
        $stmt->bind_param("ssdii",$name,$code,$price,$stock,$id);
        $stmt->execute();
        $stmt->close();
    }
    go("index.php?page=products");
}

if (isset($_GET['delete_product'])) {
    $id=(int)$_GET['delete_product'];
    $stmt=$conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $stmt->close();
    go("index.php?page=products");
}

