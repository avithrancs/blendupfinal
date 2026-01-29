<?php
// Access control & setup
require_once __DIR__ . '/../includes/functions.php';
if (!is_logged_in()) redirect('/blendupfinal/auth/login.php?next=' . urlencode('/blendupfinal/pages/checkout.php'));

$title  = 'BlendUp - Checkout & Payment';
$active = 'checkout';

// Get cart; if empty, send user back to cart page
$cart = cart_get();
if (!$cart) redirect('/blendupfinal/user/cart.php');

/* Phone validation helper */
if (!function_exists('is_valid_phone')) {
  function is_valid_phone(string $s): bool {
    $s = trim($s);
    if ($s === '') return false;
    $digits = preg_replace('/\D+/', '', $s);
    if (strlen($digits) < 7 || strlen($digits) > 15) return false;
    if (!preg_match('/^\+?[0-9\s\-\(\)]+$/', $s)) return false;
    return true;
  }
}

// Handle form submission (place order)
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); // ✅ CSRF
  $order_type     = $_POST['order_type'] ?? 'delivery';   // 'delivery' | 'pickup'
  $payment_method = $_POST['payment_method'] ?? 'cash';    // 'card' | 'cash'

  // Build address/pickup info and validate required fields
  $address = [];
  if ($order_type === 'delivery') {
    foreach (['first_name','last_name','phone','street','city','province','postal','instructions'] as $f) {
      $address[$f] = trim($_POST[$f] ?? '');
    }
    if (!$address['first_name'] || !$address['last_name'] || !$address['phone'] || !$address['street'] || !$address['city']) {
      $error = 'Please complete required delivery fields.';
    } elseif (!is_valid_phone($address['phone'])) {
      $error = 'Please enter a valid contact number.';
    }
  } else {
    foreach (['pickup_name','pickup_phone'] as $f) $address[$f] = trim($_POST[$f] ?? '');
    if (!$address['pickup_name'] || !$address['pickup_phone']) {
      $error = 'Please provide pickup name and phone.';
    } elseif (!is_valid_phone($address['pickup_phone'])) {
      $error = 'Please enter a valid pickup contact number.';
    }
  }

  if (!$error) {
    $oid = place_order($_SESSION['user']['id'], $order_type, $payment_method, json_encode($address));
    redirect('/blendupfinal/pages/order_confirmation.php?id=' . $oid);
  }
}

require __DIR__ . '/../includes/header.php';
?>
<section class="bg-gradient-to-r from-deep-teal to-olive-green text-white py-16">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
    <h2 class="text-4xl md:text-5xl font-poppins font-bold mb-4">Checkout & Payment</h2>
    <p class="text-xl text-gray-100">Complete your order and choose your preferred delivery method and payment option.</p>
  </div>
</section>

<section class="py-12 bg-soft-cream">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-3 gap-8">

    <!-- Checkout form -->
    <form method="post" class="lg:col-span-2 space-y-8">
      <?= csrf_field() ?> <!-- ✅ -->
      <?php if($error): ?>
        <div class="p-3 rounded-xl bg-red-50 text-red-700 text-sm"><?= e($error) ?></div>
      <?php endif; ?>

      <!-- Order Type -->
      <div class="bg-white rounded-3xl p-8 shadow-lg">
        <h3 class="text-2xl font-poppins font-bold text-charcoal mb-4 flex items-center">
          <i class="fas fa-truck text-deep-teal mr-3"></i> Order Type
        </h3>
        <div class="flex gap-3">
          <label class="px-4 py-2 rounded-full border cursor-pointer"><input type="radio" name="order_type" value="delivery" class="mr-2" checked>Delivery</label>
          <label class="px-4 py-2 rounded-full border cursor-pointer"><input type="radio" name="order_type" value="pickup" class="mr-2">Pickup</label>
        </div>

        <!-- Delivery -->
        <div class="mt-6 space-y-4" id="deliveryFields">
          <div class="grid md:grid-cols-2 gap-4">
            <input name="first_name" class="px-4 py-3 border rounded-2xl" placeholder="First Name *">
            <input name="last_name" class="px-4 py-3 border rounded-2xl" placeholder="Last Name *">
          </div>
          <input name="phone" inputmode="tel" pattern="^\+?[0-9\s\-()]{7,15}$" class="w-full px-4 py-3 border rounded-2xl" placeholder="Contact Number *">
          <input name="street" class="w-full px-4 py-3 border rounded-2xl" placeholder="Street Address *">
          <div class="grid md:grid-cols-3 gap-4">
            <input name="city" class="px-4 py-3 border rounded-2xl" placeholder="City *">
            <input name="province" class="px-4 py-3 border rounded-2xl" placeholder="Province">
            <input name="postal" class="px-4 py-3 border rounded-2xl" placeholder="Postal Code">
          </div>
          <textarea name="instructions" class="w-full px-4 py-3 border rounded-2xl h-24" placeholder="Delivery Instructions (Optional)"></textarea>
        </div>

        <!-- Pickup -->
        <div class="hidden mt-6 space-y-4" id="pickupFields">
          <div class="grid md:grid-cols-2 gap-4">
            <input name="pickup_name" class="px-4 py-3 border rounded-2xl" placeholder="Your Name *">
            <input name="pickup_phone" inputmode="tel" pattern="^\+?[0-9\s\-()]{7,15}$" class="px-4 py-3 border rounded-2xl" placeholder="Contact Number *">
          </div>
          <div class="bg-muted-coral/10 rounded-2xl p-4 text-sm text-charcoal">
            <i class="fas fa-clock text-muted-coral mr-2"></i> Your order will be ready for pickup in 15–20 minutes after confirmation.
          </div>
        </div>
      </div>

      <!-- Payment -->
      <div class="bg-white rounded-3xl p-8 shadow-lg">
        <h3 class="text-2xl font-poppins font-bold text-charcoal mb-4 flex items-center">
          <i class="fas fa-credit-card text-deep-teal mr-3"></i> Payment Method
        </h3>
        <div class="flex gap-3">
          <label class="px-4 py-2 rounded-full border cursor-pointer"><input type="radio" name="payment_method" value="card" class="mr-2" checked>Card</label>
          <label class="px-4 py-2 rounded-full border cursor-pointer"><input type="radio" name="payment_method" value="cash" class="mr-2">Cash on Delivery</label>
        </div>

        <div class="grid md:grid-cols-2 gap-4 mt-4" id="cardFields">
          <input class="px-4 py-3 border rounded-2xl" placeholder="Cardholder Name *">
          <input class="px-4 py-3 border rounded-2xl" placeholder="Card Number *">
          <input class="px-4 py-3 border rounded-2xl" placeholder="Expiry (MM/YY) *">
          <input class="px-4 py-3 border rounded-2xl" placeholder="CVV *">
        </div>

        <div class="hidden mt-4 text-sm text-charcoal" id="cashInfo">
          <div class="bg-olive-green/10 rounded-2xl p-4">
            <p>Pay with cash when your order arrives. Please have the exact amount ready.</p>
          </div>
        </div>
      </div>

      <button class="brand-btn">Place Order</button>
    </form>

    <!-- Order Summary -->
    <aside class="bg-white rounded-3xl p-8 shadow-lg h-fit">
      <h3 class="text-2xl font-poppins font-bold text-charcoal mb-4"><i class="fas fa-receipt text-deep-teal mr-2"></i> Order Summary</h3>
      <?php $subtotal = array_reduce($cart, fn($s,$i)=>$s+$i['price']*$i['quantity'], 0); ?>
      <div class="space-y-2 text-sm text-gray-700">
        <?php foreach($cart as $it): ?>
          <div class="flex items-center justify-between">
            <span><?= e($it['name']) ?> × <?= $it['quantity'] ?></span>
            <span class="font-semibold">$<?= number_format($it['price']*$it['quantity'],2) ?></span>
          </div>
        <?php endforeach; ?>
        <div class="border-t pt-3">
          <div class="flex justify-between"><span>Subtotal</span><span>$<?= number_format($subtotal,2) ?></span></div>
          <div class="flex justify-between"><span>Delivery</span><span>$<?= $cart? '2.50' : '0.00' ?></span></div>
          <div class="flex justify-between font-semibold"><span>Total</span><span>$<?= number_format($subtotal+($cart?2.50:0),2) ?></span></div>
        </div>
      </div>
    </aside>
  </div>
</section>

<script>
const r = (n)=>document.querySelector(n);
function toggleOrderType(v){
  const del=v==='delivery';
  r('#deliveryFields').classList.toggle('hidden',!del);
  r('#pickupFields').classList.toggle('hidden',del);
}
document.querySelectorAll('input[name=order_type]').forEach(el=>el.addEventListener('change',e=>toggleOrderType(e.target.value)));
function togglePayment(v){
  const card=v==='card';
  r('#cardFields').classList.toggle('hidden',!card);
  r('#cashInfo').classList.toggle('hidden',card);
}
document.querySelectorAll('input[name=payment_method]').forEach(el=>el.addEventListener('change',e=>togglePayment(e.target.value)));
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
