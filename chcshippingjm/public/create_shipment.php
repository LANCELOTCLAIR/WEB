<?php
require_once __DIR__ . '/../includes/auth.php';
start_secure_session();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf'] ?? '')) {
        exit('Invalid CSRF token');
    }

    $trackingId = generate_tracking_id();
    $stmt = db()->prepare('INSERT INTO shipments (customer_name, customer_email, origin, destination, service_id, tracking_id, status, created_at) VALUES (:customer_name,:customer_email,:origin,:destination,:service_id,:tracking_id,:status,NOW())');
    $stmt->execute([
        'customer_name' => $_POST['customer_name'],
        'customer_email' => $_POST['customer_email'],
        'origin' => $_POST['origin'],
        'destination' => $_POST['destination'],
        'service_id' => (int)$_POST['service_id'],
        'tracking_id' => $trackingId,
        'status' => 'Pending',
    ]);

    $shipmentId = (int) db()->lastInsertId();
    db()->prepare('INSERT INTO shipment_status_logs (shipment_id, status, notes, created_at) VALUES (:shipment_id,:status,:notes,NOW())')
        ->execute(['shipment_id' => $shipmentId, 'status' => 'Pending', 'notes' => 'Shipment created']);

    send_email($_POST['customer_email'], 'Shipment Confirmation', "Your tracking ID is {$trackingId}");
    flash('success', "Shipment created. Tracking ID: {$trackingId}");
    header('Location: create_shipment.php');
    exit;
}
$success = flash('success');
$services = db()->query('SELECT id,name FROM services ORDER BY name')->fetchAll();
?>
<!doctype html><html><head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="container py-4">
<h2>Create Shipment</h2>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<form method="post" class="row g-3">
<input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
<div class="col-md-6"><input required name="customer_name" class="form-control" placeholder="Customer name"></div>
<div class="col-md-6"><input required type="email" name="customer_email" class="form-control" placeholder="Customer email"></div>
<div class="col-md-6"><input required name="origin" class="form-control" placeholder="Origin"></div>
<div class="col-md-6"><input required name="destination" class="form-control" placeholder="Destination"></div>
<div class="col-md-6"><select class="form-select" name="service_id"><?php foreach ($services as $service): ?><option value="<?= (int)$service['id'] ?>"><?= e($service['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-12"><button class="btn btn-primary">Submit</button></div>
</form></body></html>
