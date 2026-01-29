<?php

require_once __DIR__ . '/../includes/functions.php';
// Protects page: only logged-in users can view
require __DIR__ . '/../includes/auth_check.php';

// Read order ID from query string

$order_id = (int)($_GET['id'] ?? 0);
$title    = 'Order Confirmation';


require __DIR__ . '/../includes/header.php';
?>

<!-- Hero / confirmation message -->
<section class="bg-gradient-to-r from-deep-teal to-olive-green text-white py-16 text-center">
  <div class="max-w-3xl mx-auto px-4">
    <h2 class="text-4xl font-poppins font-bold">Thank you! 🎉</h2>
    <p class="text-lg text-gray-100 mt-2">
      Your order #<?= $order_id ?> has been placed successfully.
    </p>

    <!-- Call-to-action: link to tracking page -->
    <a class="brand-btn mt-6 inline-block" href="/blendupfinal/pages/tracking.php">
      Track My Order
    </a>
  </div>
</section>

<?php 

require __DIR__ . '/../includes/footer.php'; 
?>
