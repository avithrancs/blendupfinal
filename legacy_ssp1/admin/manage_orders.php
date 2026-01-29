<?php
// Imports & Access Control
require_once __DIR__ . '/../includes/functions.php';
if (!is_admin()) redirect(BASE_URL . 'index.php');

$title  = 'Admin — Manage Orders';
$active = 'admin';
require __DIR__ . '/../includes/header.php';

// Order status labels
$STATUSES = [
  'pending'          => 'Pending',
  'preparing'        => 'Preparing',
  'out_for_delivery' => 'Out for Delivery',
  'completed'        => 'Completed',
  'cancelled'        => 'Cancelled',
];

// Handle POST actions: update status / delete order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); // CSRF
  $action   = $_POST['action'] ?? '';
  $order_id = (int)($_POST['order_id'] ?? 0);

  if ($action === 'update_status' && $order_id > 0) {
    $new = $_POST['status'] ?? 'pending';
    if (!array_key_exists($new, $STATUSES)) $new = 'pending';
    $st = $pdo->prepare("UPDATE orders SET status=? WHERE id=?");
    $st->execute([$new, $order_id]);
    redirect(BASE_URL . "admin/manage_orders.php?updated=1");
  }

  if ($action === 'delete' && $order_id > 0) {
    try {
      $pdo->beginTransaction();
      $pdo->prepare("DELETE FROM order_items WHERE order_id=?")->execute([$order_id]);
      $pdo->prepare("DELETE FROM orders WHERE id=?")->execute([$order_id]);
      $pdo->commit();
      redirect(BASE_URL . "admin/manage_orders.php?deleted=1");
    } catch (Exception $e) {
      $pdo->rollBack();
      $err = urlencode('Delete failed: '.$e->getMessage());
      redirect(BASE_URL . "admin/manage_orders.php?error=$err");
    }
  }
}

// Filtering (status / search query)
$status = $_GET['status'] ?? 'all';
$q      = trim($_GET['q'] ?? '');

// Build SQL query with filters
$params = [];
$sql = "SELECT o.*, u.name AS user_name, u.email AS user_email
        FROM orders o
        LEFT JOIN users u ON u.id = o.user_id
        WHERE 1";

if ($status !== 'all' && isset($STATUSES[$status])) {
  $sql .= " AND (o.status = ? OR o.status IS NULL)";
  $params[] = $status;
}
if ($q !== '') {
  if (ctype_digit($q)) { $sql .= " AND o.id = ?"; $params[] = (int)$q; }
  else { $sql .= " AND (u.email LIKE ? OR u.name LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; }
}
$sql .= " ORDER BY o.created_at DESC";

// Fetch orders from DB
$orders = $pdo->prepare($sql);
$orders->execute($params);
$orders = $orders->fetchAll();

// Helper: status badge display
function status_badge($s) {
  $map = [
    'pending'          => 'bg-gray-200 text-gray-800',
    'preparing'        => 'bg-olive-green/15 text-olive-green',
    'out_for_delivery' => 'bg-muted-coral/15 text-muted-coral',
    'completed'        => 'bg-deep-teal/15 text-deep-teal',
    'cancelled'        => 'bg-red-100 text-red-700',
    ''                 => 'bg-gray-200 text-gray-800',
    null               => 'bg-gray-200 text-gray-800',
  ];
  $cls = $map[$s] ?? $map[''];
  return "<span class=\"px-3 py-1 rounded-full text-xs font-semibold $cls\">".htmlspecialchars(ucwords(str_replace('_',' ',$s ?: 'pending')))."</span>";
}
?>
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
  <div class="flex items-center justify-between">
    <h1 class="font-poppins text-2xl font-bold">Manage Orders</h1>
    <a href="<?= BASE_URL ?>admin/index.php" class="text-deep-teal underline">← Back to Admin</a>
  </div>

  <?php if(isset($_GET['updated'])): ?>
    <div class="p-3 rounded-2xl bg-olive-green/15 text-olive-green text-sm">Order updated.</div>
  <?php endif; ?>
  <?php if(isset($_GET['deleted'])): ?>
    <div class="p-3 rounded-2xl bg-red-50 text-red-700 text-sm">Order deleted.</div>
  <?php endif; ?>
  <?php if(isset($_GET['error'])): ?>
    <div class="p-3 rounded-2xl bg-red-50 text-red-700 text-sm"><?= e($_GET['error']) ?></div>
  <?php endif; ?>

  <!-- Filters -->
  <form class="bg-white rounded-3xl p-6 shadow flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
    <div class="flex items-center gap-3">
      <label class="text-sm font-semibold">Status:</label>
      <div class="flex flex-wrap gap-2">
        <?php
          $tabs = ['all'=>'All'] + $STATUSES;
          foreach($tabs as $key => $label):
            $isActive = ($status===$key) ? 'bg-deep-teal text-white' : 'bg-gray-100 text-charcoal hover:bg-deep-teal hover:text-white';
        ?>
          <a class="px-4 py-2 rounded-full text-sm font-semibold <?= $isActive ?>"
             href="<?= BASE_URL ?>admin/manage_orders.php?status=<?= urlencode($key) ?>&q=<?= urlencode($q) ?>">
            <?= $label ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="relative">
      <input name="q" value="<?= e($q) ?>" class="pl-10 pr-4 py-3 rounded-2xl border" placeholder="Search by #ID, name, or email">
      <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
      <input type="hidden" name="status" value="<?= e($status) ?>">
      <button class="ml-2 px-4 py-3 rounded-2xl bg-deep-teal text-white">Search</button>
    </div>
  </form>

  <!-- Orders -->
  <section class="space-y-6">
    <?php if(!$orders): ?>
      <div class="bg-white rounded-3xl p-8 text-gray-600 text-center shadow">No orders found.</div>
    <?php endif; ?>

    <?php foreach ($orders as $o): ?>
      <?php
        $oid   = (int)$o['id'];
        $items = get_order_items($oid);
        $addr  = json_decode($o['address_json'] ?? '{}', true) ?: [];
        $total = (float)$o['total'];
      ?>
      <div class="bg-white rounded-3xl shadow p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
          <div class="flex items-center gap-4">
            <div class="text-xl font-poppins font-bold">#<?= $oid ?></div>
            <div class="text-sm text-gray-500"><?= e(date('M d, Y H:i', strtotime($o['created_at'] ?? 'now'))) ?></div>
            <div><?= status_badge($o['status'] ?? 'pending') ?></div>
          </div>
          <div class="text-sm text-gray-700">
            <span class="font-semibold"><?= e($o['user_name'] ?: 'Guest') ?></span>
            <span class="text-gray-500">·</span>
            <span><?= e($o['user_email'] ?: '-') ?></span>
          </div>
        </div>

        <div class="mt-5 grid md:grid-cols-2 gap-4">
          <div class="space-y-2">
            <?php foreach($items as $it): ?>
              <div class="flex items-start justify-between text-sm border-b pb-2">
                <div>
                  <div class="font-semibold"><?= e($it['drink_name']) ?> × <?= (int)$it['quantity'] ?></div>
                  <?php if(!empty($it['customizations'])): ?>
                    <div class="text-gray-500"><?= nl2br(e($it['customizations'])) ?></div>
                  <?php endif; ?>
                </div>
                <div class="font-semibold">$<?= number_format($it['unit_price'] * $it['quantity'], 2) ?></div>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="bg-soft-cream rounded-2xl p-4">
            <div class="flex justify-between text-sm mb-2">
              <span>Order Type</span><span class="font-semibold capitalize"><?= e($o['order_type'] ?? 'delivery') ?></span>
            </div>
            <div class="flex justify-between text-sm mb-2">
              <span>Payment</span><span class="font-semibold uppercase"><?= e($o['payment_method'] ?? 'cash') ?></span>
            </div>
            <div class="flex justify-between text-sm border-t pt-2">
              <span>Total</span><span class="font-bold text-deep-teal">$<?= number_format($total, 2) ?></span>
            </div>

            <?php if(!empty($addr)): ?>
              <div class="mt-3 text-sm">
                <div class="font-semibold mb-1">Address / Pickup</div>
                <div class="text-gray-700">
                  <?php
                    $parts = [];
                    foreach ($addr as $k=>$v) { if ($v) $parts[] = $v; }
                    echo e(implode(', ', $parts));
                  ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="mt-5 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
          <form method="post" class="flex items-center gap-2">
            <?= csrf_field() ?> 
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="order_id" value="<?= $oid ?>">
            <select name="status" class="rounded-2xl border px-3 py-2">
              <?php foreach($STATUSES as $k=>$label): ?>
                <option value="<?= $k ?>" <?= (($o['status'] ?? 'pending') === $k ? 'selected' : '') ?>><?= $label ?></option>
              <?php endforeach; ?>
            </select>
            <button class="brand-btn">Update</button>
          </form>

          <form method="post" onsubmit="return confirm('Delete this order? This cannot be undone.')" class="self-start">
            <?= csrf_field() ?> 
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="order_id" value="<?= $oid ?>">
            <button class="px-4 py-2 rounded-2xl border border-red-600 text-red-600 hover:bg-red-600 hover:text-white transition">Delete</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
