
<div class="title-row">
    <div>
        <h1>Sales History</h1>
        <div class="muted">Your latest 200 bills.</div>
    </div>
</div>

<div class="panel">
<div class="table-wrap">
<table>
<tr><th>Bill No</th><th>Date</th><th>Customer</th><th>Amount</th><th>Payment</th><th>Action</th></tr>
<?php
$sales=$conn->query("SELECT * FROM bills ORDER BY id DESC LIMIT 200");
while($s=$sales->fetch_assoc()):
?>
<tr>
    <td><b><?=e($s['bill_no'])?></b></td>
    <td><?=e($s['bill_date'])?></td>
    <td><?=e($s['customer_name'])?></td>
    <td><b>₹<?=money($s['grand_total'])?></b></td>
    <td><span class="badge"><?=e($s['payment_method'])?></span></td>
    <td><a class="btn" href="index.php?page=print_bill&id=<?=$s['id']?>">View / Print</a></td>
</tr>
<?php endwhile; ?>
</table>
</div>
</div>

