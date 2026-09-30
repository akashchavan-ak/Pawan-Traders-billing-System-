<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=e($shopName)?> - Billing</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,sans-serif;background:#f5f7fb;color:#172033}
        a{text-decoration:none}
        .topbar{height:72px;background:linear-gradient(135deg,#172554,#312e81);color:#fff;display:flex;align-items:center;justify-content:space-between;padding:0 25px;position:sticky;top:0;z-index:5}
        .brand{display:flex;align-items:center;gap:12px}
        .brand-logo{width:45px;height:45px;border-radius:13px;background:#fff;color:#312e81;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:17px}
        .brand-title{font-weight:900;font-size:19px}
        .brand-sub{font-size:11px;color:#c7d2fe;margin-top:2px}
        .user a{color:#fff;margin-left:16px;background:rgba(255,255,255,.12);padding:9px 13px;border-radius:8px}
        .layout{display:flex;min-height:calc(100vh - 72px)}
        .sidebar{width:240px;background:#0f172a;padding:20px 12px}
        .side-title{color:#64748b;font-size:11px;font-weight:bold;text-transform:uppercase;padding:5px 13px 10px;letter-spacing:1px}
        .sidebar a{display:block;color:#cbd5e1;padding:13px 14px;border-radius:10px;margin-bottom:5px;font-weight:600}
        .sidebar a:hover,.sidebar a.active{background:linear-gradient(135deg,#2563eb,#4f46e5);color:#fff}
        .content{flex:1;padding:28px;min-width:0}
        .title-row{display:flex;justify-content:space-between;align-items:center;gap:15px;margin-bottom:22px}
        h1{margin:0;font-size:27px}
        h2{margin-top:0}
        .muted{color:#64748b}
        .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:18px;margin-bottom:22px}
        .card{background:#fff;border-radius:16px;padding:20px;box-shadow:0 7px 25px rgba(15,23,42,.06);border:1px solid #eef2f7}
        .card-label{color:#64748b;font-weight:600;font-size:13px}
        .card-num{font-size:27px;font-weight:900;margin-top:8px}
        .card-icon{float:right;width:38px;height:38px;border-radius:10px;background:#eef2ff;display:flex;align-items:center;justify-content:center}
        .panel{background:#fff;border-radius:16px;padding:22px;box-shadow:0 7px 25px rgba(15,23,42,.06);border:1px solid #eef2f7;margin-bottom:20px}
        .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:15px}
        label{display:block;font-weight:700;font-size:13px;margin-bottom:7px}
        input,select,textarea{width:100%;padding:11px 12px;border:1px solid #d7dee9;border-radius:9px;font-size:14px;background:#fff}
        textarea{min-height:75px;resize:vertical}
        .btn{display:inline-block;border:0;border-radius:9px;padding:10px 15px;background:#2563eb;color:#fff;font-weight:700;cursor:pointer}
        .btn:hover{filter:brightness(.95)}
        .green{background:#16a34a}.red{background:#dc2626}.dark{background:#334155}.orange{background:#ea580c}
        .table-wrap{overflow:auto}
        table{width:100%;border-collapse:collapse;min-width:650px}
        th,td{padding:11px;border-bottom:1px solid #e5e7eb;text-align:left}
        th{background:#f8fafc;color:#475569;font-size:13px}
        .badge{display:inline-block;padding:5px 9px;border-radius:20px;background:#eef2ff;color:#3730a3;font-size:12px;font-weight:700}
        .alert{padding:12px;border-radius:10px;background:#fee2e2;color:#991b1b;margin-bottom:15px}
        .success{background:#dcfce7;color:#166534}
        .searchbox{display:flex;gap:8px;margin-bottom:15px}
        .searchbox input{flex:1}
        .customer-result{border:1px solid #e2e8f0;border-radius:12px;padding:15px;margin-top:10px;background:#f8fafc}
        .customer-result strong{font-size:17px}
        .customer-result .row{display:flex;gap:25px;flex-wrap:wrap;margin-top:7px;color:#475569}
        .quick{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:15px}
        .quick a{padding:18px;border-radius:13px;color:#fff;font-weight:800}
        .quick .q1{background:linear-gradient(135deg,#2563eb,#4f46e5)}
        .quick .q2{background:linear-gradient(135deg,#059669,#16a34a)}
        .quick .q3{background:linear-gradient(135deg,#ea580c,#f59e0b)}
        .quick .q4{background:linear-gradient(135deg,#7c3aed,#a855f7)}
        .bill-summary{display:flex;justify-content:flex-end;margin-top:18px}
        .bill-summary-box{width:360px;max-width:100%}
        .sumrow{display:flex;justify-content:space-between;padding:8px}
        .grand{border-top:2px solid #111827;font-size:21px;font-weight:900}
        .selected{border:2px solid #2563eb;background:#eff6ff}

        .billing-head{display:flex;justify-content:space-between;align-items:center;gap:15px;margin-bottom:18px;flex-wrap:wrap}
        .billing-badge{display:inline-flex;align-items:center;gap:7px;padding:8px 12px;border-radius:999px;background:#ecfdf5;color:#047857;font-size:12px;font-weight:800}
        .billing-grid{display:grid;grid-template-columns:minmax(0,1.65fr) minmax(310px,.8fr);gap:20px;align-items:start}
        .bill-panel{background:#fff;border:1px solid #e8edf5;border-radius:18px;padding:20px;box-shadow:0 10px 30px rgba(15,23,42,.07);margin-bottom:20px}
        .section-head{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:15px}
        .section-head h2{margin:0;font-size:18px}
        .mini-muted{font-size:12px;color:#64748b}
        .customer-box{display:grid;grid-template-columns:1fr auto;gap:10px}
        .customer-selected{display:flex;align-items:center;justify-content:space-between;gap:15px;padding:13px 15px;border:1px solid #bbf7d0;background:#f0fdf4;border-radius:13px;margin-top:12px}
        .customer-selected strong{color:#166534}.customer-selected small{display:block;color:#64748b;margin-top:3px}
        .bill-meta{display:grid;grid-template-columns:1fr 1fr;gap:12px}
        .meta-card{background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:12px}
        .meta-card span{display:block;color:#64748b;font-size:11px;font-weight:700;text-transform:uppercase;margin-bottom:4px}.meta-card b{font-size:14px}
        .product-tools{display:flex;gap:10px;align-items:center;margin-bottom:12px}.product-tools input{flex:1}
        .bill-table{min-width:760px}.bill-table th{font-size:12px}.bill-table td{vertical-align:middle}
        .qty-control{display:flex;align-items:center;gap:5px}.qty-control input{width:64px;text-align:center;padding:9px 5px}.qty-btn{width:31px;height:31px;border:0;border-radius:8px;background:#e2e8f0;color:#0f172a;font-weight:900;cursor:pointer}
        .price-cell{font-weight:800;color:#0f172a}.stock-pill{display:inline-block;margin-top:4px;font-size:11px;color:#64748b}
        .row-total{font-weight:900;color:#15803d;white-space:nowrap}.remove-row{background:#fee2e2;color:#b91c1c;border:0;border-radius:8px;width:34px;height:34px;cursor:pointer;font-weight:900}
        .summary-panel{position:sticky;top:90px}.summary-brand{display:flex;align-items:center;gap:12px;padding-bottom:16px;border-bottom:1px dashed #cbd5e1}.summary-logo{width:50px;height:50px;border-radius:50%;border:3px solid #f59e0b;color:#b45309;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:20px}.summary-brand h2{margin:0;font-size:20px}.summary-brand small{color:#64748b}
        .sumline{display:flex;justify-content:space-between;padding:10px 0;color:#475569}.sumline strong{color:#0f172a}.sumline.discount strong{color:#dc2626}.sum-grand{display:flex;justify-content:space-between;align-items:center;padding:16px 0;border-top:2px solid #0f172a;border-bottom:1px solid #e2e8f0;font-size:22px;font-weight:900}.sum-grand span:last-child{color:#15803d}
        .payment-box{margin-top:15px;padding:14px;border-radius:12px;background:#f8fafc;border:1px solid #e2e8f0}.payment-box .grid{gap:10px}.change-box{margin-top:10px;padding:10px 12px;border-radius:9px;background:#ecfdf5;color:#166534;font-weight:800;display:flex;justify-content:space-between}
        .bill-actions{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:15px}.bill-actions .btn{padding:13px;text-align:center}.btn.blue{background:#2563eb}.btn.purple{background:#4f46e5}
        .empty-items{padding:30px;text-align:center;color:#64748b;border:2px dashed #dbe3ef;border-radius:12px;margin-top:10px}
        .percent-input{display:flex;align-items:center}.percent-input input{border-radius:10px 0 0 10px}.percent-input span{height:42px;display:flex;align-items:center;padding:0 13px;background:#f1f5f9;border:1px solid #cbd5e1;border-left:0;border-radius:0 10px 10px 0;font-weight:800;color:#475569}.shortcut{font-size:11px;color:#64748b;text-align:center;margin-top:10px}
        @media(max-width:1000px){.billing-grid{grid-template-columns:1fr}.summary-panel{position:static}}
        @media(max-width:600px){.customer-box,.bill-meta,.bill-actions{grid-template-columns:1fr}.bill-panel{padding:15px}}
        @media(max-width:800px){
            .sidebar{width:205px}.content{padding:16px}.topbar{padding:0 12px}.brand-title{font-size:16px}.user span{display:none}
        }
        @media(max-width:600px){
            .layout{display:block}.sidebar{width:100%;display:flex;overflow:auto;padding:8px}.side-title{display:none}.sidebar a{white-space:nowrap;margin:0 3px}.content{padding:12px}.topbar{height:64px}.brand-sub{display:none}
        }
    </style>
</head>
<body>

<div class="topbar">
    <div class="brand">
        <div class="brand-logo">PT</div>
        <div>
            <div class="brand-title"><?=e($shopName)?></div>
            <div class="brand-sub"><?=e($shopTagline)?></div>
        </div>
    </div>
    <div class="user">
        <span>Welcome, <?=e($_SESSION['username'])?></span>
        <a href="index.php?logout=1">Logout</a>
    </div>
</div>

<div class="layout">

<aside class="sidebar">
    <div class="side-title">Main Menu</div>
    <a class="<?=$page==='dashboard'?'active':''?>" href="index.php">📊 Dashboard</a>
    <a class="<?=$page==='billing'?'active':''?>" href="index.php?page=billing">🧾 New Billing</a>
    <a class="<?=$page==='customers'?'active':''?>" href="index.php?page=customers">👥 Customers</a>
    <a class="<?=$page==='products'?'active':''?>" href="index.php?page=products">📦 Products & Stock</a>
    <a class="<?=$page==='sales'?'active':''?>" href="index.php?page=sales">📋 Sales History</a>
</aside>

<main class="content">
