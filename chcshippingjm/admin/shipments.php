<?php
require_once __DIR__ . '/../includes/auth.php';
start_secure_session();
require_auth('admin');

$statuses = ['Pending','In Transit','Arrived','Out for Delivery','Delivered'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!validate_csrf($_POST['csrf'] ?? '')) { exit('Invalid CSRF'); }
  $shipmentId = (int) $_POST['shipment_id'];
  $status = $_POST['status'];
  if (!in_array($status, $statuses, true)) { exit('Invalid status'); }
  $tracking = trim($_POST['tracking_id']);

  db()->prepare('UPDATE shipments SET tracking_id=:tracking_id,status=:status,updated_at=NOW() WHERE id=:id')
    ->execute(['tracking_id'=>$tracking,'status'=>$status,'id'=>$shipmentId]);
  db()->prepare('INSERT INTO shipment_status_logs (shipment_id,status,notes,created_at) VALUES (:shipment_id,:status,:notes,NOW())')
    ->execute(['shipment_id'=>$shipmentId,'status'=>$status,'notes'=>'Status updated by admin']);

  $ship = db()->prepare('SELECT customer_email FROM shipments WHERE id=:id');
  $ship->execute(['id'=>$shipmentId]);
  $email = $ship->fetchColumn();
  if ($email) send_email($email, 'Shipment Status Updated', "Your shipment status is now {$status}.");
  flash('success','Shipment updated.');
  header('Location: shipments.php'); exit;
}
$rows = db()->query('SELECT id,customer_name,tracking_id,status FROM shipments ORDER BY created_at DESC')->fetchAll();
$msg = flash('success');
?>
<!doctype html><html><head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="container py-4">
<h2>Admin Shipment Operations</h2>
<?php if($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<table class="table table-striped"><tr><th>Customer</th><th>Tracking</th><th>Status</th><th>Update</th></tr><?php foreach($rows as $r): ?><tr><form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="shipment_id" value="<?= (int)$r['id'] ?>"><td><?= e($r['customer_name']) ?></td><td><input class="form-control" name="tracking_id" value="<?= e($r['tracking_id']) ?>"></td><td><select class="form-select" name="status"><?php foreach($statuses as $s): ?><option <?= $r['status']===$s?'selected':'' ?>><?= e($s) ?></option><?php endforeach; ?></select></td><td><button class="btn btn-sm btn-primary">Save</button></td></form></tr><?php endforeach; ?></table>
</body></html>
