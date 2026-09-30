<?php
/* ---------- CREATE BILL ---------- */

$billError="";

if(isset($_POST['create_bill'])){
    $customerId=(int)($_POST['customer_id'] ?? 0);
    $customerName=trim($_POST['customer_name'] ?? "");
    $customerMobile=trim($_POST['customer_mobile'] ?? "");
    $discountPercent=max(0,min(100,(float)($_POST['discount_percent'] ?? 0)));
    $discount=0;
    $payment=$_POST['payment_method'] ?? "Cash";

    if($customerId>0){
        $stmt=$conn->prepare("SELECT customer_name,mobile FROM customers WHERE id=? LIMIT 1");
        $stmt->bind_param("i",$customerId);
        $stmt->execute();
        $c=$stmt->get_result()->fetch_assoc();
        $stmt->close();
        if($c){
            $customerName=$c['customer_name'];
            $customerMobile=$c['mobile'];
        }
    }

    if($customerName==="") $customerName="Walk-in Customer";
    if(!in_array($payment,["Cash","UPI","Credit"],true)) $payment="Cash";

    $productIds=$_POST['product_id'] ?? [];
    $qtys=$_POST['quantity'] ?? [];
    $items=[];
    $subtotal=0;

    if(is_array($productIds) && is_array($qtys)){
        for($i=0;$i<count($productIds);$i++){
            $pid=(int)($productIds[$i] ?? 0);
            $qty=(int)($qtys[$i] ?? 0);
            if($pid<=0 || $qty<=0) continue;

            $stmt=$conn->prepare("SELECT id,product_name,price,stock FROM products WHERE id=? LIMIT 1");
            $stmt->bind_param("i",$pid);
            $stmt->execute();
            $p=$stmt->get_result()->fetch_assoc();
            $stmt->close();

            if(!$p) continue;
            if($qty>(int)$p['stock']) $qty=(int)$p['stock'];
            if($qty<=0) continue;

            $amount=$qty*(float)$p['price'];
            $subtotal+=$amount;

            $items[]=[
                "id"=>$p['id'],
                "name"=>$p['product_name'],
                "qty"=>$qty,
                "price"=>(float)$p['price'],
                "amount"=>$amount
            ];
        }
    }

    if(!$items){
        $billError="Please select at least one product with available stock.";
    }else{
        if($discountPercent>100) $discountPercent=100;
        $discount=round($subtotal*($discountPercent/100),2);
        if($discount>$subtotal) $discount=$subtotal;
        $grand=$subtotal-$discount;
        $billNo="PT-".date("YmdHis")."-".rand(100,999);

        $conn->begin_transaction();
        try{
            $stmt=$conn->prepare("INSERT INTO bills(bill_no,customer_id,customer_name,customer_mobile,subtotal,discount,grand_total,payment_method) VALUES(?,?,?,?,?,?,?,?)");
            $stmt->bind_param("sissddds",$billNo,$customerId,$customerName,$customerMobile,$subtotal,$discount,$grand,$payment);
            $stmt->execute();
            $billId=$conn->insert_id;
            $stmt->close();

            foreach($items as $it){
                $stmt=$conn->prepare("INSERT INTO bill_items(bill_id,product_id,product_name,quantity,price,amount) VALUES(?,?,?,?,?,?)");
                $stmt->bind_param("iisidd",$billId,$it['id'],$it['name'],$it['qty'],$it['price'],$it['amount']);
                $stmt->execute();
                $stmt->close();

                $stmt=$conn->prepare("UPDATE products SET stock=stock-? WHERE id=?");
                $stmt->bind_param("ii",$it['qty'],$it['id']);
                $stmt->execute();
                $stmt->close();
            }

            $conn->commit();
            go("index.php?page=print_bill&id=".$billId);
        }catch(Exception $ex){
            $conn->rollback();
            $billError="Bill could not be created. Please check your database.";
        }
    }
}

