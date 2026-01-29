<?php
require_once __DIR__ . '/../includes/functions.php';

$id    = (int)($_GET['id'] ?? 0);
$drink = $id ? get_drink($id) : null;
if (!$drink) redirect(BASE_URL . 'pages/menu.php');

/* Helpers */
if (!function_exists('img_src')) {
  function img_src($path) {
    if (!$path) return BASE_URL . 'assets/img/placeholder.jpg';
    if (preg_match('~^https?://~i', $path)) return $path;
    return BASE_URL . ltrim($path, '/');
  }
}
if (!function_exists('pseudo_rating')) {
  function pseudo_rating(string $name): string {
    $seed = crc32(strtolower($name));
    return number_format(4.3 + (($seed % 70) / 100), 1); // 4.3–4.9
  }
}

/* Handle Add-to-Cart (POST) */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); // ✅ CSRF
  $sugarMap = ['0'=>'No Sugar','1'=>'Low','2'=>'Medium','3'=>'High'];
  $iceMap   = ['0'=>'No Ice','1'=>'Light','2'=>'Medium','3'=>'Extra'];

  $sugar = $sugarMap[$_POST['sugar'] ?? '2'] ?? 'Medium';
  $ice   = $iceMap[$_POST['ice']   ?? '2'] ?? 'Medium';

  $toppings    = $_POST['toppings'] ?? [];
  $addonPrices = ['Chia'=>0.50, 'Protein'=>1.00, 'Honey'=>0.30];

  $addonCost = 0.0;
  $tops = [];
  foreach ($toppings as $t) {
    $t = trim($t);
    if ($t === '') continue;
    $addonCost += $addonPrices[$t] ?? 0;
    $tops[] = "+$t";
  }

  $qty        = max(1, (int)($_POST['qty'] ?? 1));
  $unit_price = (float)$drink['price'] + $addonCost;

  $customizations = "Sugar: $sugar; Ice: $ice" . (count($tops) ? '; ' . implode(', ', $tops) : '');
  cart_add($drink, $qty, $customizations, $unit_price);

  redirect(BASE_URL . 'user/cart.php');
}

$rating = pseudo_rating($drink['name']);
$title  = 'Customize Drink';
require __DIR__ . '/../includes/header.php';
?>
<section class="py-4 bg-white border-b">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <a href="<?= BASE_URL ?>pages/menu.php" class="inline-flex items-center text-deep-teal hover:text-opacity-80">
      <i class="fas fa-arrow-left mr-2"></i> Back to Menu
    </a>
  </div>
</section>

<form method="post" class="py-12">
  <?= csrf_field() ?> <!-- ✅ -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-12 items-start">

    <!-- Left: product -->
    <div class="space-y-6">
      <div class="relative">
        <div class="bg-white rounded-3xl p-8 shadow-2xl">
          <img src="<?= e(img_src($drink['image_url'])) ?>" alt="<?= e($drink['name']) ?>" class="w-full h-96 object-cover rounded-2xl">
          <?php if(!empty($drink['is_featured'])): ?>
          <div class="absolute top-4 right-4 bg-olive-green text-white px-4 py-2 rounded-full text-sm font-semibold">Popular</div>
          <?php endif; ?>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-6 shadow">
        <h2 class="text-3xl font-poppins font-bold text-charcoal mb-3"><?= e($drink['name']) ?></h2>
        <p class="text-gray-600 mb-4"><?= e($drink['description'] ?? 'Naturally sweet, blended fresh to order. Great taste, great energy.') ?></p>

        <div class="flex items-center space-x-2 mb-4">
          <div class="flex text-yellow-400">
            <?php
              $f=(int)floor($rating); $h=((float)$rating-$f)>=0.5?1:0; $e=5-$f-$h;
              for($i=0;$i<$f;$i++) echo '<i class="fas fa-star"></i>';
              if($h) echo '<i class="fas fa-star-half-alt"></i>';
              for($i=0;$i<$e;$i++) echo '<i class="far fa-star"></i>';
            ?>
          </div>
          <span class="text-sm text-gray-500">(<?= $rating ?>)</span>
        </div>

        <div class="flex items-center justify-between">
          <span class="text-sm text-gray-500">Base Price:</span>
          <span id="base-price" data-base="<?= e($drink['price']) ?>" class="text-xl font-bold text-deep-teal">$<?= number_format($drink['price'],2) ?></span>
        </div>
      </div>
    </div>

    <!-- Right: controls -->
    <div class="space-y-6">

      <div class="text-center">
        <h3 class="text-2xl font-poppins font-bold text-charcoal">Customize Your Drink</h3>
        <p class="text-gray-600">Make it perfect for your taste</p>
      </div>

      <!-- Sugar -->
      <div class="bg-white rounded-3xl p-6 shadow">
        <div class="flex justify-between items-center mb-4">
          <h4 class="text-lg font-semibold text-charcoal">Sugar Level</h4>
          <span id="sugar-label" class="text-deep-teal font-medium">Medium</span>
        </div>
        <input type="range" name="sugar" id="sugar-slider" min="0" max="3" value="2"
               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
               oninput="document.getElementById('sugar-label').textContent=['No Sugar','Low','Medium','High'][this.value]">
        <div class="flex justify-between text-xs text-gray-500 mt-2">
          <span>No Sugar</span><span>Low</span><span>Medium</span><span>High</span>
        </div>
      </div>

      <!-- Ice -->
      <div class="bg-white rounded-3xl p-6 shadow">
        <div class="flex justify-between items-center mb-4">
          <h4 class="text-lg font-semibold text-charcoal">Ice Level</h4>
          <span id="ice-label" class="text-deep-teal font-medium">Medium</span>
        </div>
        <input type="range" name="ice" id="ice-slider" min="0" max="3" value="2"
               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
               oninput="document.getElementById('ice-label').textContent=['No Ice','Light','Medium','Extra'][this.value]">
        <div class="flex justify-between text-xs text-gray-500 mt-2">
          <span>No Ice</span><span>Light</span><span>Medium</span><span>Extra</span>
        </div>
      </div>

      <!-- Toppings -->
      <div class="bg-white rounded-3xl p-6 shadow">
        <h4 class="text-lg font-semibold text-charcoal mb-4">Add Toppings</h4>

        <label class="flex items-center justify-between p-3 bg-gray-50 rounded-xl cursor-pointer">
          <span class="flex items-center gap-3">
            <input type="checkbox" name="toppings[]" value="Chia" data-price="0.5" class="w-5 h-5 text-deep-teal">
            <span>
              <span class="text-charcoal font-medium block">Chia Seeds</span>
              <span class="text-sm text-gray-500">Rich in omega-3 and fiber</span>
            </span>
          </span>
          <span class="text-deep-teal font-semibold">+$0.50</span>
        </label>

        <label class="flex items-center justify-between p-3 bg-gray-50 rounded-xl cursor-pointer mt-3">
          <span class="flex items-center gap-3">
            <input type="checkbox" name="toppings[]" value="Protein" data-price="1.0" class="w-5 h-5 text-deep-teal">
            <span>
              <span class="text-charcoal font-medium block">Protein Powder</span>
              <span class="text-sm text-gray-500">Vanilla whey boost</span>
            </span>
          </span>
          <span class="text-deep-teal font-semibold">+$1.00</span>
        </label>

        <label class="flex items-center justify-between p-3 bg-gray-50 rounded-xl cursor-pointer mt-3">
          <span class="flex items-center gap-3">
            <input type="checkbox" name="toppings[]" value="Honey" data-price="0.3" class="w-5 h-5 text-deep-teal">
            <span>
              <span class="text-charcoal font-medium block">Honey Drizzle</span>
              <span class="text-sm text-gray-500">Natural sweetness</span>
            </span>
          </span>
          <span class="text-deep-teal font-semibold">+$0.30</span>
        </label>
      </div>

      <!-- Summary / Quantity / Submit -->
      <div class="bg-white rounded-3xl p-6 shadow sticky bottom-6">
        <div class="flex justify-between text-sm mb-2">
          <span>Base Price:</span>
          <span>$<?= number_format($drink['price'],2) ?></span>
        </div>
        <div class="flex justify-between items-center text-lg font-semibold">
          <span>Total:</span>
          <span id="total-amount" data-total="<?= e($drink['price']) ?>" class="text-deep-teal">
            $<?= number_format($drink['price'],2) ?>
          </span>
        </div>

        <div class="mt-4 flex items-center justify-between">
          <span class="text-sm">Quantity:</span>
          <div class="flex items-center gap-3">
            <button type="button" id="qty-minus" class="w-8 h-8 rounded-full border flex items-center justify-center">−</button>
            <input type="text" id="qty" name="qty" value="1" class="w-10 text-center border rounded-md py-1" />
            <button type="button" id="qty-plus" class="w-8 h-8 rounded-full border flex items-center justify-center">+</button>
          </div>
        </div>

        <button class="w-full mt-5 bg-muted-coral text-white px-6 py-3 rounded-full hover:bg-opacity-90 transition">
          <span id="btn-label">Add to Cart - $<?= number_format($drink['price'],2) ?></span>
        </button>
      </div>
    </div>
  </div>
</form>

<script>
  function $$(sel, root=document){ return Array.from(root.querySelectorAll(sel)); }
  const base = parseFloat(document.getElementById('base-price').dataset.base || '0');

  function calc() {
    let addons = 0;
    $$('input[name="toppings[]"]:checked').forEach(cb => {
      addons += parseFloat(cb.dataset.price || '0');
    });
    const qty = Math.max(1, parseInt(document.getElementById('qty').value || '1', 10));
    const unit = base + addons;
    const total = unit * qty;

    document.getElementById('total-amount').textContent = '$' + total.toFixed(2);
    document.getElementById('btn-label').textContent   = 'Add to Cart - $' + total.toFixed(2);
    document.getElementById('qty').value = qty;
  }

  $$('input[name="toppings[]"]').forEach(cb => cb.addEventListener('change', calc));
  document.getElementById('qty-minus').addEventListener('click', () => {
    document.getElementById('qty').value = Math.max(1, parseInt(document.getElementById('qty').value||'1',10)-1);
    calc();
  });
  document.getElementById('qty-plus').addEventListener('click',  () => {
    document.getElementById('qty').value = Math.max(1, parseInt(document.getElementById('qty').value||'1',10)+1);
    calc();
  });
  document.getElementById('qty').addEventListener('input', calc);

  calc();
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
