<?php
require_once __DIR__ . '/../includes/functions.php';

$title  = 'Shopping Cart — BlendUp';
$active = 'cart';

/* ---------- actions ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); // ✅ CSRF
  $action = $_POST['action'] ?? '';

  if ($action === 'inc' || $action === 'dec') {
    $key = $_POST['key'] ?? '';
    $cart = cart_get();
    if (isset($cart[$key])) {
      $q = (int)$cart[$key]['quantity'];
      $q = $action === 'inc' ? $q + 1 : max(1, $q - 1);
      cart_update($key, $q);
    }
    redirect(BASE_URL . 'user/cart.php');
  }

  if ($action === 'remove') {
    cart_remove($_POST['key'] ?? '');
    redirect(BASE_URL . 'user/cart.php');
  }

  if ($action === 'clear') {
    cart_clear();
    redirect(BASE_URL . 'user/cart.php');
  }
}

$cart = cart_get();

/* pricing */
$DELIVERY_FEE = $cart ? 2.99 : 0.00;
$TAX_RATE     = 0.08; // 8%
$itemsCount   = array_sum(array_map(fn($i)=>(int)$i['quantity'], $cart));
$subtotal     = array_reduce($cart, fn($s,$i)=>$s + ($i['price']*$i['quantity']), 0.0);
$tax          = $cart ? round($subtotal * $TAX_RATE, 2) : 0.00;
$total        = $subtotal + $DELIVERY_FEE + $tax;

/* helpers */
if (!function_exists('img_src')) {
  function img_src($path) {
    if (!$path) return BASE_URL . 'assets/img/placeholder.jpg';
    if (preg_match('~^https?://~i', $path)) return $path;
    return BASE_URL . ltrim($path, '/');
  }
}
function chips_from_custom(string $s): array {
  $chips = [];
  foreach (array_filter(array_map('trim', preg_split('~;~', $s))) as $part) {
    foreach (array_filter(array_map('trim', explode(',', $part))) as $c) {
      if ($c !== '') $chips[] = $c;
    }
  }
  return $chips;
}

require __DIR__ . '/../includes/header.php';
?>

<!-- HERO -->
<section class="bg-gradient-to-r from-deep-teal to-olive-green text-white py-14">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
    <h1 class="text-4xl md:text-5xl font-poppins font-bold">Shopping Cart</h1>
    <p class="text-white/90 mt-2">Review your fresh selections and customize your perfect order.</p>
  </div>
</section>

<!-- BODY -->
<section class="py-10 bg-soft-cream min-h-[60vh]">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-3 gap-8">

    <!-- LEFT: Items card -->
    <div class="lg:col-span-2">
      <div class="bg-white rounded-3xl shadow p-6">
        <h3 class="font-poppins font-bold text-lg mb-4">Your Cart Items</h3>

        <?php if (!$cart): ?>
          <div class="text-center text-gray-600 py-10">
            Your cart is empty.
            <a href="<?= BASE_URL ?>pages/menu.php" class="text-deep-teal underline">Browse the menu</a>
          </div>
        <?php else: ?>

          <div class="space-y-4">
            <?php foreach ($cart as $key => $it):
              $drink = get_drink($it['drink_id']);
              $img   = $drink ? img_src($drink['image_url']) : BASE_URL.'assets/img/placeholder.jpg';
              $chips = $it['customizations'] ? chips_from_custom($it['customizations']) : [];
            ?>
            <div class="flex items-center justify-between gap-4  rounded-2xl px-4 py-3">
              <!-- left group: thumb + meta -->
              <div class="flex items-center gap-4">
                <img src="<?= e($img) ?>" class="w-14 h-14 object-cover rounded-xl" alt="">
                <div>
                  <div class="font-semibold text-charcoal leading-tight"><?= e($it['name']) ?></div>
                  <!-- chips -->
                  <?php if($chips): ?>
                  <div class="flex flex-wrap gap-2 mt-1 ">
                    <?php foreach ($chips as $c): ?>
                      <span class="text-[11px] px-2 py-1 rounded-full bg-soft-cream border text-charcoal/80"><?= e($c) ?></span>
                    <?php endforeach; ?>
                  </div>
                  <?php endif; ?>
                  <div class="text-sm text-muted-coral mt-1 font-semibold">$<?= number_format($it['price'], 2) ?></div>
                </div>
              </div>

              <!-- right: qty stepper + trash -->
              <div class="flex items-center gap-2">
                <form method="post">
                  <?= csrf_field() ?> <!-- ✅ -->
                  <input type="hidden" name="action" value="dec">
                  <input type="hidden" name="key" value="<?= e($key) ?>">
                  <button aria-label="decrease" class="w-9 h-9 rounded-full border flex items-center justify-center hover:bg-gray-50">−</button>
                </form>
                <div class="w-10 text-center text-sm"><?= (int)$it['quantity'] ?></div>
                <form method="post">
                  <?= csrf_field() ?> <!-- ✅ -->
                  <input type="hidden" name="action" value="inc">
                  <input type="hidden" name="key" value="<?= e($key) ?>">
                  <button aria-label="increase" class="w-9 h-9 rounded-full border flex items-center justify-center hover:bg-gray-50">+</button>
                </form>
                <form method="post" onsubmit="return confirm('Remove this item?')">
                  <?= csrf_field() ?> <!-- ✅ -->
                  <input type="hidden" name="action" value="remove">
                  <input type="hidden" name="key" value="<?= e($key) ?>">
                  <button aria-label="remove"
                          class="w-9 h-9 rounded-full border border-red-500 text-red-600 flex items-center justify-center hover:bg-red-500 hover:text-white transition">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

          <form method="post" class="mt-5" onsubmit="return confirm('Clear the entire cart?')">
            <?= csrf_field() ?> <!-- ✅ -->
            <input type="hidden" name="action" value="clear">
            <button class="px-4 py-2 rounded-xl border text-gray-700 hover:bg-gray-100">Clear Cart</button>
          </form>

        <?php endif; ?>
      </div>
    </div>

    <!-- RIGHT: Summary card -->
    <aside class="bg-white rounded-3xl shadow p-6 h-fit">
      <h3 class="font-poppins font-bold text-lg mb-4">Order Summary</h3>

      <div class="space-y-2 text-sm text-gray-700">
        <div class="flex justify-between">
          <span>Subtotal<?= $itemsCount ? " ({$itemsCount} item".($itemsCount>1?'s':'').")" : '' ?></span>
          <span>$<?= number_format($subtotal, 2) ?></span>
        </div>
        <div class="flex justify-between">
          <span>Delivery Fee</span>
          <span>$<?= number_format($DELIVERY_FEE, 2) ?></span>
        </div>
        <div class="flex justify-between">
          <span>Tax</span>
          <span>$<?= number_format($tax, 2) ?></span>
        </div>
        <div class="flex justify-between font-semibold text-base pt-2 border-t">
          <span>Total</span>
          <span class="text-deep-teal">$<?= number_format($total, 2) ?></span>
        </div>
      </div>

      <!-- Promo (visual) -->
      <div class="flex gap-2 mt-4">
        <input class="flex-1 px-3 py-2 border rounded-xl" placeholder="Promo code">
        <button class="px-4 py-2 rounded-xl border">Apply</button>
      </div>

      <a href="<?= BASE_URL ?>pages/checkout.php"
         class="mt-4 w-full inline-flex items-center justify-center gap-2 bg-deep-teal text-white px-4 py-3 rounded-2xl hover:bg-opacity-90 transition">
        <i class="fas fa-shopping-bag"></i>
        Proceed to Checkout
      </a>

      <a href="<?= BASE_URL ?>pages/menu.php"
         class="mt-3 w-full inline-flex items-center justify-center gap-2 bg-muted-coral text-white px-4 py-3 rounded-2xl hover:bg-opacity-90 transition">
        <i class="fas fa-arrow-left"></i>
        Continue Shopping
      </a>

      <div class="mt-4 space-y-2 text-sm text-charcoal">
        <div class="flex items-start gap-2">
          <i class="fas fa-badge-check mt-0.5 text-deep-teal"></i>
          <span>Free delivery on orders over $50</span>
        </div>
        <div class="flex items-start gap-2">
          <i class="fas fa-clock mt-0.5 text-muted-coral"></i>
          <span>Estimated delivery: 25–35 minutes</span>
        </div>
      </div>
    </aside>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
