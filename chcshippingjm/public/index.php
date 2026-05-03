<?php
require_once __DIR__ . '/../includes/auth.php';
start_secure_session();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CHC Shipping JM</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="p-5 mb-4 bg-primary text-white rounded-3">
    <h1 class="display-5 fw-bold">Shipping Customs Brokerage Limited</h1>
    <p class="col-md-8 fs-4">Time is money; we save you both. Buy from anywhere and ship to Jamaica.</p>
    <a class="btn btn-danger btn-lg" href="create_shipment.php">Create Shipment</a>
  </div>

  <form class="card p-3 mb-4" method="get" action="track.php">
    <label class="form-label fw-semibold">Track shipment</label>
    <div class="input-group">
      <input class="form-control" name="tracking_id" placeholder="Enter tracking ID (e.g CHCABC123)">
      <button class="btn btn-primary">Search</button>
    </div>
  </form>

  <div class="row g-3">
    <?php foreach ([
      'Express' => 'Fast city-to-city delivery for urgent parcels.',
      'Air Freight' => 'Priority international air cargo handling.',
      'Sea Freight' => 'Cost-effective container shipments worldwide.',
      'Door-to-Door' => 'End-to-end pickup, customs, and final mile delivery.'
    ] as $name => $text): ?>
      <div class="col-md-6 col-lg-3">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5><?= e($name) ?></h5>
            <p class="text-muted mb-0"><?= e($text) ?></p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>
