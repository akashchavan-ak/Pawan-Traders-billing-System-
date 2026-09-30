
<div class="title-row">
    <div>
        <h1>Products & Stock</h1>
        <div class="muted">Manage product names, prices and available stock.</div>
    </div>
</div>

<div class="panel">
    <h2><?=$editProduct ? 'Edit Product' : 'Add Product'?></h2>
    <form method="post">
        <?php if($editProduct): ?><input type="hidden" name="product_id" value="<?=$editProduct['id']?>"><?php endif; ?>
        <div class="grid">
            <div><label>Product Name</label><input name="product_name" required value="<?=e($editProduct['product_name'] ?? '')?>"></div>
            <div><label>Product Code</label><input name="product_code" value="<?=e($editProduct['product_code'] ?? '')?>"></div>
            <div><label>Selling Price</label><input type="number" step="0.01" min="0" name="price" required value="<?=e($editProduct['price'] ?? '')?>"></div>
            <div><label>Stock Quantity</label><input type="number" min="0" name="stock" required value="<?=e($editProduct['stock'] ?? '')?>"></div>
        </div>
        <br>
        <?php if($editProduct): ?>
            <button class="btn green" name="update_product">Update Product</button>
            <a class="btn dark" href="index.php?page=products">Cancel</a>
        <?php else: ?>
            <button class="btn" name="add_product">+ Add Product</button>
        <?php endif; ?>
    </form>
</div>

<div class="panel">
    <h2>Product List</h2>
    <div class="table-wrap">
    <table>
        <tr><th>ID</th><th>Product</th><th>Code</th><th>Price</th><th>Stock</th><th>Status</th><th>Action</th></tr>
        <?php
        $products=$conn->query("SELECT * FROM products ORDER BY id DESC");
        while($p=$products->fetch_assoc()):
        ?>
        <tr>
            <td><?=$p['id']?></td>
            <td><b><?=e($p['product_name'])?></b></td>
            <td><?=e($p['product_code'])?></td>
            <td>₹<?=money($p['price'])?></td>
            <td><?=$p['stock']?></td>
            <td><?=$p['stock']<=5?'<span class="badge" style="background:#fee2e2;color:#991b1b">Low Stock</span>':'<span class="badge" style="background:#dcfce7;color:#166534">Available</span>'?></td>
            <td>
                <a class="btn dark" href="index.php?page=products&edit_product=<?=$p['id']?>">Edit</a>
                <a class="btn red" href="index.php?page=products&delete_product=<?=$p['id']?>" onclick="return confirm('Delete this product?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    </div>
</div>

