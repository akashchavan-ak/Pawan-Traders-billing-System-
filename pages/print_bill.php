<?php
/**
 * Print / view a single bill. Expects $conn, $shopName, $upiId, e(), money(), upiQrUrl() to be available.
 */
    $id=(int)($_GET['id'] ?? 0);

    $stmt=$conn->prepare("SELECT * FROM bills WHERE id=? LIMIT 1");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $bill=$stmt->get_result()->fetch_assoc();
    $stmt->close();

    if(!$bill) die("Bill not found.");

    $stmt=$conn->prepare("SELECT * FROM bill_items WHERE bill_id=? ORDER BY id ASC");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $items=$stmt->get_result();

    $itemRows=[];
    $itemCount=0;
    while($row=$items->fetch_assoc()){
        $itemRows[]=$row;
        $itemCount += (int)$row['quantity'];
    }

    $billDate=date('d M Y, h:i A',strtotime($bill['bill_date']));
    $customerName=trim($bill['customer_name'])!=='' ? $bill['customer_name'] : 'Walk-in Customer';
    $customerMobile=trim($bill['customer_mobile'])!=='' ? $bill['customer_mobile'] : '-';
    $customerId=(int)($bill['customer_id'] ?? 0);
    $discount=(float)$bill['discount'];
    $subtotal=(float)$bill['subtotal'];
    $grand=(float)$bill['grand_total'];
    $qrUrl=upiQrUrl($upiId,$shopName,$grand,$bill['bill_no']);
    ?>
    <!doctype html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title><?=e($bill['bill_no'])?> - Pawan Traders</title>
        <style>
            *{box-sizing:border-box}
            :root{--ink:#172033;--muted:#64748b;--line:#dbe3ee;--blue:#1d4ed8;--gold:#d99b18}
            body{margin:0;background:#eef2f7;font-family:Arial,Helvetica,sans-serif;color:var(--ink);padding:24px}
            .actions{max-width:860px;margin:0 auto 16px;text-align:right}
            .actions a,.actions button{display:inline-block;padding:11px 17px;margin-left:7px;border:0;border-radius:9px;background:#1d4ed8;color:#fff;text-decoration:none;cursor:pointer;font-weight:700}
            .actions .secondary{background:#334155}
            .invoice{max-width:860px;margin:auto;background:#fff;padding:30px 34px;box-shadow:0 12px 35px rgba(15,23,42,.10);border-radius:16px}
            .topbar{height:7px;background:linear-gradient(90deg,#0f172a 0 68%,#d99b18 68% 100%);border-radius:8px 8px 0 0;margin:-30px -34px 24px}
            .brand{display:flex;align-items:center;justify-content:space-between;gap:20px;padding-bottom:22px;border-bottom:1px solid var(--line)}
            .brand-left{display:flex;align-items:center;gap:16px}
            .logo{width:74px;height:74px;border:3px solid var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--gold);font-weight:900;font-size:28px;font-family:Georgia,serif;position:relative;flex:none}
            .logo:after{content:"";position:absolute;inset:6px;border:1px solid var(--gold);border-radius:50%}
            .brand h1{margin:0;font-size:29px;letter-spacing:.4px}
            .tagline{margin:4px 0 0;color:var(--blue);font-weight:800;font-size:13px;letter-spacing:.8px}
            .contact{font-size:12px;color:var(--muted);line-height:1.65;margin-top:5px}
            .invoice-title{text-align:right}
            .invoice-title .label{display:inline-block;padding:7px 12px;border-radius:999px;background:#eff6ff;color:var(--blue);font-weight:900;font-size:12px;letter-spacing:1px}
            .invoice-title h2{margin:8px 0 0;font-size:25px}
            .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin:20px 0}
            .info-box{border:1px solid var(--line);border-radius:12px;padding:14px 16px;background:#fbfdff}
            .info-label{font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--muted);font-weight:800;margin-bottom:6px}
            .info-main{font-weight:800;font-size:14px}
            .info-small{font-size:12px;color:var(--muted);margin-top:4px}
            table{width:100%;border-collapse:separate;border-spacing:0;overflow:hidden;border:1px solid var(--line);border-radius:12px}
            th,td{padding:11px 10px;border-bottom:1px solid var(--line);font-size:12px}
            th{background:#f1f5f9;text-align:left;color:#334155;font-size:10px;text-transform:uppercase;letter-spacing:.6px}
            th:nth-child(1),td:nth-child(1){width:42px;text-align:center}
            th:nth-child(3),td:nth-child(3){width:65px;text-align:center}
            th:nth-child(4),td:nth-child(4),th:nth-child(5),td:nth-child(5){text-align:right;white-space:nowrap}
            tr:last-child td{border-bottom:0}
            .totals-area{display:grid;grid-template-columns:1fr 340px;gap:25px;margin-top:20px;align-items:end}
            .note-box{border:1px dashed #cbd5e1;border-radius:12px;padding:14px;color:var(--muted);font-size:11px;line-height:1.6}
            .note-box strong{color:#334155}
            .totals{border:1px solid var(--line);border-radius:12px;padding:10px 15px;background:#fbfdff}
            .tr{display:flex;justify-content:space-between;gap:20px;padding:7px 0;font-size:13px}
            .discount{color:#15803d}
            .grand{margin:7px -15px -10px;padding:13px 15px;border-top:1px solid var(--line);background:#effdf4;border-radius:0 0 12px 12px;font-size:18px;font-weight:900;color:#166534}
            .payment{display:flex;justify-content:space-between;margin-top:12px;padding:10px 12px;border-radius:9px;background:#f8fafc;font-size:12px}
            .thanks{text-align:center;margin-top:26px;padding-top:18px;border-top:1px solid var(--line)}
            .thanks strong{display:block;font-size:15px;margin-bottom:4px}
            .thanks span{font-size:11px;color:var(--muted)}
            .footer{display:flex;justify-content:space-between;gap:20px;margin-top:16px;font-size:9px;color:#94a3b8}
            .pay-area{display:grid;grid-template-columns:1fr 190px;gap:18px;margin-top:18px;align-items:stretch}
            .upi-box{border:1px solid var(--line);border-radius:12px;padding:15px;background:#fff;text-align:center}
            .upi-box h3{margin:0 0 5px;font-size:13px}.upi-box p{margin:4px 0;font-size:10px;color:var(--muted)}
            .upi-box img{width:118px;height:118px;display:block;margin:9px auto 5px}.upi-id{font-weight:800;font-size:11px;word-break:break-all}
            .upi-placeholder{padding:22px 8px;border:1px dashed #cbd5e1;border-radius:9px;color:#64748b;font-size:10px}

            @media(max-width:650px){.pay-area{grid-template-columns:1fr}body{padding:10px}.invoice{padding:20px 16px;border-radius:10px}.topbar{margin:-20px -16px 18px}.brand{align-items:flex-start}.brand-left{gap:10px}.logo{width:58px;height:58px;font-size:21px}.brand h1{font-size:21px}.invoice-title h2{font-size:19px}.info-grid,.totals-area{grid-template-columns:1fr}.invoice-title{text-align:left}.actions{text-align:center}.actions a,.actions button{margin:3px}.footer{flex-direction:column;gap:4px}}
            @media print{
                @page{size:A4;margin:10mm}
                body{background:#fff;padding:0}
                .actions{display:none}
                .invoice{width:100%;max-width:none;margin:0;padding:0;box-shadow:none;border-radius:0}
                .topbar{margin:0 0 18px;border-radius:0}
                .info-box,.totals,.note-box{break-inside:avoid}
                table{break-inside:auto}
                tr{break-inside:avoid;break-after:auto}
            }
        </style>
    </head>
    <body>
        <div class="actions">
            <button onclick="window.print()">🖨 Print Bill</button>
            <a href="index.php?page=billing" class="secondary">＋ New Bill</a>
            <a href="index.php" class="secondary">Dashboard</a>
        </div>

        <div class="invoice">
            <div class="topbar"></div>

            <div class="brand">
                <div class="brand-left">
                    <div class="logo">PT</div>
                    <div>
                        <h1><?=e($shopName)?></h1>
                        <div class="tagline"><?=e($shopTagline)?></div>
                        <div class="contact">📍 <?=e($shopAddress)?> &nbsp; • &nbsp; 📞 <?=e($shopPhone)?></div>
                    </div>
                </div>
                <div class="invoice-title">
                    <span class="label">PAYMENT RECEIPT</span>
                    <h2>INVOICE</h2>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-label">Bill Information</div>
                    <div class="info-main">Bill No: <?=e($bill['bill_no'])?></div>
                    <div class="info-small">Date: <?=e($billDate)?></div>
                </div>
                <div class="info-box">
                    <div class="info-label">Customer</div>
                    <div class="info-main"><?=e($customerName)?></div>
                    <div class="info-small">Mobile: <?=e($customerMobile)?><?=($customerId>0?' &nbsp; • &nbsp; ID: CUST-'.str_pad($customerId,4,'0',STR_PAD_LEFT):'')?></div>
                </div>
            </div>

            <table>
                <tr><th>#</th><th>Product / Item</th><th>Qty</th><th>Rate</th><th>Amount</th></tr>
                <?php $n=1; foreach($itemRows as $it): ?>
                <tr>
                    <td><?=$n++?></td>
                    <td><strong><?=e($it['product_name'])?></strong></td>
                    <td><?=$it['quantity']?></td>
                    <td>₹<?=money($it['price'])?></td>
                    <td><strong>₹<?=money($it['amount'])?></strong></td>
                </tr>
                <?php endforeach; ?>
            </table>

            <div class="totals-area">
                <div class="note-box">
                    <strong>Thank you for shopping with Pawan Traders.</strong><br>
                    Please keep this bill for your records. Products are billed as per the quantities and prices shown above.
                </div>
                <div>
                    <div class="totals">
                        <div class="tr"><span>Items / Qty</span><b><?=$itemCount?></b></div>
                        <div class="tr"><span>Subtotal</span><b>₹<?=money($subtotal)?></b></div>
                        <div class="tr discount"><span>Discount (<?=number_format($subtotal>0 ? ($discount/$subtotal)*100 : 0,2)?>%)</span><b>− ₹<?=money($discount)?></b></div>
                        <div class="tr grand"><span>Grand Total</span><b>₹<?=money($grand)?></b></div>
                    </div>
                    <div class="payment"><span>Payment Method</span><strong><?=e($bill['payment_method'])?></strong></div>
                </div>
            </div>

            <div class="thanks">
                <strong>Thank You! Visit Again 😊</strong>
                <span>We appreciate your business • Computer generated bill</span>
            </div>
            <div class="footer">
                <span>PAWAN TRADERS • Smart Billing &amp; Inventory</span>
                <span>Bill: <?=e($bill['bill_no'])?></span>
            </div>
        </div>
    </body>
    </html>
<?php exit;
