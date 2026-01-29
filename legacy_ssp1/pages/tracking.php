<?php

require_once __DIR__ . '/../includes/functions.php';
// Protects page: only logged-in users can view
require __DIR__ . '/../includes/auth_check.php';

$title = 'BlendUp — Order Tracking';
require __DIR__ . '/../includes/header.php';


// Fetch all orders for the current user

$orders = get_user_orders($_SESSION['user']['id']);
?>

<main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <!-- Page heading -->
  <h2 class="font-poppins text-2xl font-bold">Your Orders</h2>

  <!-- Orders grid -->
  <div class="mt-6 grid md:grid-cols-2 gap-6">
    <?php foreach($orders as $o): $items = get_order_items($o['id']); ?>
    <div class="bg-white rounded-3xl p-5 shadow">
      
      <!-- Header: order id + status -->
      <div class="flex items-center justify-between">
        <div>
          <div class="text-xs text-[#280a3e]/60">Order ID</div>
          <div class="font-poppins font-bold">#<?= $o['id'] ?></div>
        </div>
        <div class="text-sm px-3 py-1 rounded-full bg-[#f9cb99] font-poppins font-semibold">
          <?= ucfirst($o['status']) ?>
        </div>
      </div>

      <!-- Items list -->
      <ul class="mt-3 text-sm list-disc list-inside text-[#280a3e]/80">
        <?php foreach($items as $it): ?>
          <li><?= e($it['drink_name']) ?> × <?= $it['quantity'] ?></li>
        <?php endforeach; ?>
      </ul>

      <!-- Progress bar -->
      <div class="mt-5">
        <div class="flex justify-between text-xs text-[#280a3e]/60 mb-2">
          <span>Pending</span><span>Preparing</span><span>Delivered</span>
        </div>
        <?php 
          // Quick % estimate for status
          $pct = $o['status']==='pending'   ? 10
               : ($o['status']==='preparing'? 60
               : 100);
        ?>
        <div class="w-full h-3 bg-[#f2edd1] rounded-full overflow-hidden">
          <div class="h-full bg-[#689b8a] rounded-full" style="width: <?= $pct ?>%"></div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
