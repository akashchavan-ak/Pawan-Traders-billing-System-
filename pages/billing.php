
<div class="billing-head">
    <div>
        <h1>New Billing</h1>
        <div class="muted">Create a fast GST-free bill with live totals and stock checking.</div>
    </div>
    <div class="billing-badge">● Billing system ready</div>
</div>

<?php if($billError): ?><div class="alert"><?=e($billError)?></div><?php endif; ?>

<div class="billing-grid">
    <div>
        <div class="bill-panel">
            <div class="section-head">
                <div><h2>👤 Customer</h2><div class="mini-muted">Search by customer ID, name or mobile number.</div></div>
                <a class="btn dark" href="index.php?page=customers">+ Add Customer</a>
            </div>

            <form method="get" class="customer-box">
                <input type="hidden" name="page" value="billing">
                <input id="customerSearch" name="customer_search" placeholder="🔎  Search customer by ID, name or mobile..." value="<?=e($_GET['customer_search'] ?? '')?>">
                <button class="btn" type="submit">Search</button>
            </form>

            <?php
            if(isset($_GET['customer_search']) && trim($_GET['customer_search'])!==""){
                $q=trim($_GET['customer_search']);
                if(ctype_digit($q)){
                    $num=(int)$q;
                    $stmt=$conn->prepare("SELECT * FROM customers WHERE id=? OR customer_name LIKE ? OR mobile LIKE ? ORDER BY customer_name");
                    $like="%".$q."%";
                    $stmt->bind_param("iss",$num,$like,$like);
                }else{
                    $like="%".$q."%";
                    $stmt=$conn->prepare("SELECT * FROM customers WHERE customer_name LIKE ? OR mobile LIKE ? ORDER BY customer_name");
                    $stmt->bind_param("ss",$like,$like);
                }
                $stmt->execute(); $rs=$stmt->get_result();
                if($rs->num_rows===0){
                    echo '<div class="alert" style="margin-top:12px">No customer found. Try another name, ID or mobile number.</div>';
                }else{
                    while($c=$rs->fetch_assoc()):
            ?>
                <div class="customer-result">
                    <strong><?=e($c['customer_name'])?></strong>
                    <div class="row"><span>ID: <?=$c['id']?></span><span>Mobile: <?=e($c['mobile'])?></span><a class="btn green" href="index.php?page=billing&customer_id=<?=$c['id']?>">Use Customer</a></div>
                </div>
            <?php endwhile; } $stmt->close(); } ?>

            <?php if($selectedCustomer): ?>
            <div class="customer-selected">
                <div><strong>✓ <?=e($selectedCustomer['customer_name'])?></strong><small>ID: <?=$selectedCustomer['id']?> • <?=e($selectedCustomer['mobile'])?></small></div>
                <a class="btn dark" href="index.php?page=billing">Change</a>
            </div>
            <?php else: ?>
            <div class="customer-selected" style="background:#f8fafc;border-color:#e2e8f0"><div><strong style="color:#334155">Walk-in Customer</strong><small>No customer selected — you can still create the bill.</small></div></div>
            <?php endif; ?>
        </div>

        <form method="post" id="billingForm">
            <input type="hidden" name="customer_id" value="<?=$selectedCustomer['id'] ?? 0?>">
            <div class="bill-panel">
                <div class="section-head">
                    <div><h2>🧾 Bill Details</h2><div class="mini-muted">Bill number is generated automatically after saving.</div></div>
                    <button type="button" class="btn orange" onclick="addRow()">+ Add Product</button>
                </div>
                <div class="bill-meta">
                    <div class="meta-card"><span>Customer</span><b><?=e($selectedCustomer['customer_name'] ?? 'Walk-in Customer')?></b></div>
                    <div class="meta-card"><span>Date</span><b><?=date('d M Y, h:i A')?></b></div>
                </div>

                <div class="product-tools" style="margin-top:16px">
                    <input id="productFilter" placeholder="🔎 Type product name to filter the list..."><span class="mini-muted">Select a product below</span>
                </div>

                <div class="table-wrap">
                    <table class="bill-table" id="itemsTable">
                        <thead><tr><th>#</th><th>Product</th><th>Price</th><th>Qty</th><th>Stock</th><th>Total</th><th>Remove</th></tr></thead>
                        <tbody id="itemBody"></tbody>
                    </table>
                </div>
                <div class="empty-items" id="emptyItems">No products added yet. Click <b>+ Add Product</b> to start the bill.</div>
            </div>

            <div class="bill-panel">
                <div class="section-head"><div><h2>💳 Payment</h2><div class="mini-muted">Choose how the customer is paying.</div></div></div>
                <div class="grid">
                    <div><label>Overall Discount (%)</label><div class="percent-input"><input id="discountInput" type="number" name="discount_percent" min="0" max="100" step="0.01" value="0"><span>%</span></div><div class="mini-muted" id="discountHint">₹0.00 discount</div></div>
                    <div><label>Payment Method</label><select id="paymentMethod" name="payment_method"><option>Cash</option><option>UPI</option><option>Credit</option></select></div>
                </div>
                <div class="grid" id="receivedWrap" style="margin-top:12px">
                    <div><label>Amount Received (₹)</label><input id="receivedInput" type="number" min="0" step="0.01" placeholder="Enter received amount"></div>
                    <div><label>Change to Return</label><input id="changeInput" type="text" value="₹0.00" readonly></div>
                </div>
            </div>

            <button type="submit" class="btn green" name="create_bill" style="display:none" id="hiddenSave"></button>
        </form>
    </div>

    <aside class="bill-panel summary-panel">
        <div class="summary-brand"><div class="summary-logo">PT</div><div><h2>Pawan Traders</h2><small>Smart Billing & Inventory</small></div></div>
        <div style="margin-top:12px">
            <div class="sumline"><span>Items</span><strong id="sumItems">0</strong></div>
            <div class="sumline"><span>Subtotal</span><strong id="sumSubtotal">₹0.00</strong></div>
            <div class="sumline discount"><span>Discount <small id="sumDiscountPct">(0%)</small></span><strong id="sumDiscount">− ₹0.00</strong></div>
            <div class="sum-grand"><span>Grand Total</span><span id="sumGrand">₹0.00</span></div>
        </div>
        <div class="payment-box"><div class="sumline" style="padding:0"><span>Payment</span><strong id="sumPayment">Cash</strong></div><div class="change-box"><span>Balance</span><span id="sumBalance">₹0.00</span></div></div>
        <div class="bill-actions"><button type="button" class="btn dark" onclick="clearBill()">Clear Bill</button><button type="button" class="btn blue" onclick="submitBill()">💾 Save Bill</button></div>
        <button type="button" class="btn green" style="width:100%;margin-top:10px" onclick="submitBill(true)">🖨 Save & Print</button>
        <div class="shortcut">F9 = Save Bill • Ctrl + Enter = Save & Print</div>
    </aside>
</div>

<?php
$jsProducts=[];
$allProducts=$conn->query("SELECT id,product_name,price,stock FROM products WHERE stock>0 ORDER BY product_name ASC");
while($p=$allProducts->fetch_assoc()) $jsProducts[]=$p;
?>
<script>
const products = <?=json_encode($jsProducts, JSON_UNESCAPED_UNICODE)?>;
const body = document.getElementById('itemBody');
const emptyItems = document.getElementById('emptyItems');
let rowNo = 0;
function moneyJS(n){return '₹'+Number(n||0).toFixed(2)}
function productOptions(filter=''){
  const f=filter.toLowerCase();
  return '<option value="">-- Select Product --</option>'+products.filter(p=>p.product_name.toLowerCase().includes(f)).map(p=>`<option value="${p.id}" data-price="${p.price}" data-stock="${p.stock}">${escapeHtml(p.product_name)} — ₹${Number(p.price).toFixed(2)}</option>`).join('');
}
function escapeHtml(s){return String(s).replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]))}
function addRow(){
  rowNo++;
  emptyItems.style.display='none';
  const tr=document.createElement('tr'); tr.dataset.row=rowNo;
  tr.innerHTML=`<td class="row-number">${rowNo}</td><td><select name="product_id[]" class="product-select" onchange="updateRow(this)">${productOptions(document.getElementById('productFilter').value)}</select></td><td class="price-cell">₹0.00</td><td><div class="qty-control"><button type="button" class="qty-btn" onclick="stepQty(this,-1)">−</button><input name="quantity[]" type="number" min="1" value="1" onchange="updateRow(this)"><button type="button" class="qty-btn" onclick="stepQty(this,1)">+</button></div></td><td class="stock-pill">—</td><td class="row-total">₹0.00</td><td><button type="button" class="remove-row" onclick="removeRow(this)">×</button></td>`;
  body.appendChild(tr); renumber(); updateSummary();
}
function updateRow(el){
  const tr=el.closest('tr'); const select=tr.querySelector('.product-select'); const opt=select.options[select.selectedIndex]; const price=Number(opt?.dataset.price||0); const stock=Number(opt?.dataset.stock||0); const qty=tr.querySelector('input[name="quantity[]"]');
  if(stock && Number(qty.value)>stock) qty.value=stock;
  tr.querySelector('.price-cell').textContent=moneyJS(price); tr.querySelector('.stock-pill').textContent=stock?`Stock: ${stock}`:'—'; tr.querySelector('.row-total').textContent=moneyJS(price*Number(qty.value||0)); updateSummary();
}
function stepQty(btn,delta){const input=btn.parentElement.querySelector('input'); let v=Math.max(1,Number(input.value||1)+delta); const stock=Number(btn.closest('tr').querySelector('.product-select option:checked')?.dataset.stock||0); if(stock)v=Math.min(v,stock); input.value=v; updateRow(input)}
function removeRow(btn){btn.closest('tr').remove(); renumber(); if(!body.children.length)emptyItems.style.display='block'; updateSummary()}
function renumber(){[...body.children].forEach((tr,i)=>tr.querySelector('.row-number').textContent=i+1)}
function updateSummary(){let sub=0,items=0; [...body.children].forEach(tr=>{const opt=tr.querySelector('.product-select option:checked'); const price=Number(opt?.dataset.price||0); const qty=Number(tr.querySelector('input[name="quantity[]"]')?.value||0); sub+=price*qty; items+=qty;}); const pct=Math.min(100,Math.max(0,Number(document.getElementById('discountInput').value||0))); const dis=Math.min(sub,sub*pct/100); const grand=sub-dis; document.getElementById('sumItems').textContent=items; document.getElementById('sumSubtotal').textContent=moneyJS(sub); document.getElementById('sumDiscount').textContent='− '+moneyJS(dis); document.getElementById('sumDiscountPct').textContent='('+pct.toFixed(2).replace(/\.00$/,'')+'%)'; document.getElementById('discountHint').textContent=moneyJS(dis)+' discount on this bill'; document.getElementById('sumGrand').textContent=moneyJS(grand); document.getElementById('sumPayment').textContent=document.getElementById('paymentMethod').value; const rec=Number(document.getElementById('receivedInput').value||0); const bal=Math.max(0,rec-grand); document.getElementById('sumBalance').textContent=moneyJS(bal); document.getElementById('changeInput').value=moneyJS(bal)}
function clearBill(){if(confirm('Clear all products and payment details?')){body.innerHTML='';emptyItems.style.display='block';document.getElementById('discountInput').value=0;document.getElementById('receivedInput').value='';updateSummary();addRow()}}
function submitBill(print=false){if(!body.children.length){alert('Please add at least one product.');return} let valid=false; [...body.children].forEach(tr=>{if(tr.querySelector('.product-select').value)valid=true}); if(!valid){alert('Please select at least one product.');return} if(print) document.getElementById('billingForm').setAttribute('data-print','1'); document.getElementById('hiddenSave').click()}
document.getElementById('discountInput').addEventListener('input',updateSummary); document.getElementById('receivedInput').addEventListener('input',updateSummary); document.getElementById('paymentMethod').addEventListener('change',updateSummary);
document.getElementById('productFilter').addEventListener('input',()=>{document.querySelectorAll('.product-select').forEach(s=>{const old=s.value;s.innerHTML=productOptions(document.getElementById('productFilter').value);s.value=old;updateRow(s)})});
document.addEventListener('keydown',e=>{if(e.key==='F9'){e.preventDefault();submitBill(false)} if(e.ctrlKey&&e.key==='Enter'){e.preventDefault();submitBill(true)}});
addRow(); updateSummary();
</script>
