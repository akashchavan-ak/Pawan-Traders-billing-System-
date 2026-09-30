
<?php
$pcount=(int)$conn->query("SELECT COUNT(*) total FROM products")->fetch_assoc()['total'];
$ccount=(int)$conn->query("SELECT COUNT(*) total FROM customers")->fetch_assoc()['total'];
$bcount=(int)$conn->query("SELECT COUNT(*) total FROM bills")->fetch_assoc()['total'];
$today=(float)$conn->query("SELECT COALESCE(SUM(grand_total),0) total FROM bills WHERE DATE(bill_date)=CURDATE()")->fetch_assoc()['total'];
$low=(int)$conn->query("SELECT COUNT(*) total FROM products WHERE stock<=5")->fetch_assoc()['total'];
?>

<div class="title-row">
    <div>
        <h1>Dashboard</h1>
        <div class="muted">Welcome to your Pawan Traders billing system</div>
    </div>
    <a class="btn" href="index.php?page=billing">+ New Bill</a>
</div>

<div class="cards">
    <div class="card"><div class="card-icon">💰</div><div class="card-label">Today's Sales</div><div class="card-num">₹<?=money($today)?></div></div>
    <div class="card"><div class="card-icon">🧾</div><div class="card-label">Total Bills</div><div class="card-num"><?=$bcount?></div></div>
    <div class="card"><div class="card-icon">📦</div><div class="card-label">Products</div><div class="card-num"><?=$pcount?></div></div>
    <div class="card"><div class="card-icon">👥</div><div class="card-label">Customers</div><div class="card-num"><?=$ccount?></div></div>
    <div class="card"><div class="card-icon">⚠️</div><div class="card-label">Low Stock Items</div><div class="card-num"><?=$low?></div></div>
</div>

<div class="panel">
    <h2>Quick Actions</h2>
    <div class="quick">
        <a class="q1" href="index.php?page=billing">🧾<br><br>Create New Bill</a>
        <a class="q2" href="index.php?page=customers">👥<br><br>Find Customer</a>
        <a class="q3" href="index.php?page=products">📦<br><br>Manage Products</a>
        <a class="q4" href="index.php?page=sales">📋<br><br>View Sales</a>
    </div>
</div>

<div class="panel">
    <h2>Recent Bills</h2>
    <div class="table-wrap">
    <table>
        <tr><th>Bill No</th><th>Date</th><th>Customer</th><th>Amount</th><th>Payment</th><th></th></tr>
        <?php
        $recent=$conn->query("SELECT * FROM bills ORDER BY id DESC LIMIT 8");
        while($s=$recent->fetch_assoc()):
        ?>
        <tr>
            <td><?=e($s['bill_no'])?></td>
            <td><?=e($s['bill_date'])?></td>
            <td><?=e($s['customer_name'])?></td>
            <td><b>₹<?=money($s['grand_total'])?></b></td>
            <td><span class="badge"><?=e($s['payment_method'])?></span></td>
            <td><a class="btn dark" href="index.php?page=print_bill&id=<?=$s['id']?>">View</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
    </div>
</div>

