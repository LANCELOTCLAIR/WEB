<?php
require_once __DIR__ . '/../includes/auth.php';
start_secure_session();
$trackingId = $_GET['tracking_id'] ?? '';
$shipment = null;
$logs = [];
if ($trackingId !== '') {
  $stmt = db()->prepare('SELECT s.*, sv.name AS service_name FROM shipments s LEFT JOIN services sv ON sv.id=s.service_id WHERE s.tracking_id=:tracking_id LIMIT 1');
  $stmt->execute(['tracking_id' => $trackingId]);
  $shipment = $stmt->fetch();
  if ($shipment) {
    $l = db()->prepare('SELECT status, notes, created_at FROM shipment_status_logs WHERE shipment_id=:shipment_id ORDER BY created_at DESC');
    $l->execute(['shipment_id' => $shipment['id']]);
    $logs = $l->fetchAll();
  }
}
?>
<!doctype html><html><head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="container py-4">
<h2>Track Shipment</h2>
<?php if ($trackingId && !$shipment): ?><div class="alert alert-warning">Tracking ID not found.</div><?php endif; ?>
<?php if ($shipment): ?><div class="card p-3"><h4><?= e($shipment['tracking_id']) ?> - <?= e($shipment['status']) ?></h4><p><?= e($shipment['origin']) ?> → <?= e($shipment['destination']) ?></p><ul><?php foreach($logs as $log): ?><li><?= e($log['created_at']) ?>: <?= e($log['status']) ?> (<?= e($log['notes']) ?>)</li><?php endforeach; ?></ul></div><?php endif; ?>
</body></html>
