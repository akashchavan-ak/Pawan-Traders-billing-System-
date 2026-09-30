
<div class="title-row">
    <div>
        <h1>Customers</h1>
        <div class="muted">Search by customer ID or name before creating a bill.</div>
    </div>
</div>

<div class="panel">
    <h2>🔎 Search Customer</h2>
    <form method="get" class="searchbox">
        <input type="hidden" name="page" value="customers">
        <input name="q" value="<?=e($_GET['q'] ?? '')?>" placeholder="Enter customer ID or customer name...">
        <button class="btn" type="submit">Search</button>
        <a class="btn dark" href="index.php?page=customers">Clear</a>
    </form>

    <?php
    if(isset($_GET['q']) && trim($_GET['q'])!==""){
        $q=trim($_GET['q']);
        if(ctype_digit($q)){
            $num=(int)$q;
            $stmt=$conn->prepare("SELECT * FROM customers WHERE id=? OR customer_name LIKE ? ORDER BY customer_name");
            $like="%".$q."%";
            $stmt->bind_param("is",$num,$like);
        }else{
            $like="%".$q."%";
            $stmt=$conn->prepare("SELECT * FROM customers WHERE customer_name LIKE ? OR mobile LIKE ? ORDER BY customer_name");
            $stmt->bind_param("ss",$like,$like);
        }
        $stmt->execute();
        $results=$stmt->get_result();

        if($results->num_rows===0){
            echo '<div class="alert">No customer found.</div>';
        }else{
            while($c=$results->fetch_assoc()):
    ?>
        <div class="customer-result">
            <strong><?=e($c['customer_name'])?></strong>
            <div class="row">
                <span><b>ID:</b> <?=$c['id']?></span>
                <span><b>Mobile:</b> <?=e($c['mobile'])?></span>
                <span><b>Address:</b> <?=e($c['address'])?></span>
                <a class="btn green" href="index.php?page=billing&customer_id=<?=$c['id']?>">Use for Billing →</a>
                <a class="btn dark" href="index.php?page=customers&edit_customer=<?=$c['id']?>">Edit</a>
            </div>
        </div>
    <?php
            endwhile;
        }
        $stmt->close();
    }
    ?>
</div>

<div class="panel">
    <h2><?=$editCustomer ? 'Edit Customer' : 'Add New Customer'?></h2>
    <form method="post">
        <?php if($editCustomer): ?>
            <input type="hidden" name="customer_id" value="<?=$editCustomer['id']?>">
        <?php endif; ?>
        <div class="grid">
            <div><label>Customer Name</label><input name="customer_name" required value="<?=e($editCustomer['customer_name'] ?? '')?>"></div>
            <div><label>Mobile Number</label><input name="mobile" value="<?=e($editCustomer['mobile'] ?? '')?>"></div>
            <div><label>Address</label><textarea name="address"><?=e($editCustomer['address'] ?? '')?></textarea></div>
        </div>
        <br>
        <?php if($editCustomer): ?>
            <button class="btn green" name="update_customer">Update Customer</button>
            <a class="btn dark" href="index.php?page=customers">Cancel</a>
        <?php else: ?>
            <button class="btn" name="add_customer">+ Add Customer</button>
        <?php endif; ?>
    </form>
</div>

<div class="panel">
    <h2>All Customers</h2>
    <div class="table-wrap">
    <table>
        <tr><th>ID</th><th>Name</th><th>Mobile</th><th>Address</th><th>Action</th></tr>
        <?php
        $all=$conn->query("SELECT * FROM customers ORDER BY id DESC");
        while($c=$all->fetch_assoc()):
        ?>
        <tr>
            <td><?=$c['id']?></td>
            <td><b><?=e($c['customer_name'])?></b></td>
            <td><?=e($c['mobile'])?></td>
            <td><?=e($c['address'])?></td>
            <td>
                <a class="btn green" href="index.php?page=billing&customer_id=<?=$c['id']?>">Bill</a>
                <a class="btn dark" href="index.php?page=customers&edit_customer=<?=$c['id']?>">Edit</a>
                <a class="btn red" href="index.php?page=customers&delete_customer=<?=$c['id']?>" onclick="return confirm('Delete this customer?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    </div>
</div>

