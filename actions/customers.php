<?php
/* ---------- CUSTOMER ACTIONS ---------- */

if (isset($_POST['add_customer'])) {
    $name=trim($_POST['customer_name'] ?? "");
    $mobile=trim($_POST['mobile'] ?? "");
    $address=trim($_POST['address'] ?? "");

    if ($name !== "") {
        $stmt=$conn->prepare("INSERT INTO customers(customer_name,mobile,address) VALUES(?,?,?)");
        $stmt->bind_param("sss",$name,$mobile,$address);
        $stmt->execute();
        $stmt->close();
    }
    go("index.php?page=customers");
}

if (isset($_POST['update_customer'])) {
    $id=(int)($_POST['customer_id'] ?? 0);
    $name=trim($_POST['customer_name'] ?? "");
    $mobile=trim($_POST['mobile'] ?? "");
    $address=trim($_POST['address'] ?? "");

    if($id>0 && $name!==""){
        $stmt=$conn->prepare("UPDATE customers SET customer_name=?,mobile=?,address=? WHERE id=?");
        $stmt->bind_param("sssi",$name,$mobile,$address,$id);
        $stmt->execute();
        $stmt->close();
    }
    go("index.php?page=customers");
}

if (isset($_GET['delete_customer'])) {
    $id=(int)$_GET['delete_customer'];
    $stmt=$conn->prepare("DELETE FROM customers WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $stmt->close();
    go("index.php?page=customers");
}

