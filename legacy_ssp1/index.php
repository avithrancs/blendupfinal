<?php

// Dev diagnostics (errors to browser) — remove/disable in production

ini_set('display_errors', 1);
error_reporting(E_ALL);


require __DIR__ . '/includes/functions.php';
$title  = 'BlendUp - Sip Fresh. Live Fresh.';
$active = 'home';
require __DIR__ . '/includes/header.php';


// View helper: normalize image paths (with placeholder fallback)
// (Kept local; you can move to functions.php if you want it global)

if (!function_exists('img_src')) {
  function img_src($path) {
    if (!$path) return BASE_URL . 'assets/img/placeholder.jpg';
    if (preg_match('~^https?://~i', $path)) return $path;
    return BASE_URL . ltrim($path, '/');
  }
}


// Data: pull "Today's Specials" (latest 3 drinks)

$specials = get_drinks();  // already ordered by created_at desc
$specials = array_slice($specials, 0, 3);
?>

<!-- HERO section -->
<section class="bg-gradient-to-r from-deep-teal to-olive-green text-white py-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid md:grid-cols-2 gap-12 items-center">
      <div class="space-y-6">
        <h2 class="text-4xl md:text-6xl font-poppins font-bold leading-tight">
          Fresh Juices & Smoothies <span class="text-muted-coral">Delivered</span>
        </h2>
        <p class="text-xl text-gray-100 font-light leading-relaxed">
          Experience the perfect blend of taste and nutrition with our premium selection of fresh juices and smoothies, crafted with love and delivered to your doorstep.
        </p>
        <div class="flex flex-col sm:flex-row gap-4">
          <a href="<?= BASE_URL ?>pages/menu.php" class="bg-muted-coral text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-opacity-90 transition-all duration-300 text-center transform hover:scale-105">
            Order Now
          </a>
          <a href="<?= BASE_URL ?>pages/menu.php" class="border-2 border-white text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-white hover:text-deep-teal transition-all duration-300 text-center">
            View Menu
          </a>
        </div>
      </div>

      <!-- Hero image card -->
      <div class="relative">
        <div class="bg-white rounded-3xl p-8 shadow-2xl rotate-1">
          <img src="https://images.unsplash.com/photo-1546173159-315724a31696?w=900&auto=format&fit=crop" class="w-full h-80 object-cover rounded-2xl" alt="">
          <div class="absolute -top-4 -right-4 bg-muted-coral text-white rounded-full w-16 h-16 flex items-center justify-center font-bold text-lg shadow">
            Fresh!
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!--  TODAY'S SPECIALS -->
<section id="featured" class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Section header -->
    <div class="text-center mb-16">
      <h3 class="text-4xl font-poppins font-bold text-charcoal mb-4">Today's Specials</h3>
      <p class="text-xl text-gray-600 max-w-2xl mx-auto">Handpicked fresh ingredients blended to perfection. Start your healthy journey with our signature drinks.</p>
    </div>

    <?php
    
    // View-only helpers used by the specials cards
    // (guarded with function_exists so duplication is safe)
   
    if (!function_exists('pseudo_rating')) {
      function pseudo_rating(string $name): string {
        $seed = crc32(strtolower($name));
        $v = 4.3 + (($seed % 70) / 100); // 4.30..4.99
        return number_format($v, 1);
      }
    }
    if (!function_exists('badge_for')) {
      function badge_for(array $d): array {
        $name = strtolower($d['name'] ?? '');
        $cat  = strtolower($d['category'] ?? '');
        if (!empty($d['is_featured']))                      return ['Popular','bg-olive-green'];
        if (str_contains($name,'shot'))                     return ['Shot','bg-deep-teal'];
        if (str_contains($name,'refresh'))                  return ['Refreshing','bg-olive-green'];
        if ($cat === 'seasonal')                            return ['New','bg-muted-coral'];
        if (str_contains($name,'detox') || str_contains($name,'green')) return ['Detox','bg-olive-green'];
        if (str_contains($name,'orange'))                   return ['Classic','bg-deep-teal'];
        return ['Bestseller','bg-deep-teal'];
      }
    }
    if (!function_exists('short_desc')) {
      function short_desc(array $d): string {
        $name = strtolower($d['name'] ?? '');
        if (str_contains($name,'green'))    return 'Spinach, banana, apple, ginger & coconut water. Packed with vitamins & minerals.';
        if (str_contains($name,'tropical')) return 'Mango, pineapple, passion fruit & coconut milk. A tropical escape in every sip.';
        if (str_contains($name,'orange'))   return '100% pure fresh orange juice. No additives—just vitamin C goodness.';
        if (str_contains($name,'carrot'))   return 'Fresh carrot and ginger. Perfect immunity booster.';
        if (str_contains($name,'berry'))    return 'Mixed berries blended smooth. Antioxidant rich and delicious.';
        if (str_contains($name,'citrus'))   return 'Orange, lemon & lime. Bright, zesty, super refreshing.';
        return 'Naturally sweet, blended fresh to order. Great taste, great energy.';
      }
    }
    ?>

    <!-- Specials grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php if ($specials): foreach ($specials as $d):
        [$badge,$badgeClass] = badge_for($d);
        $rating = pseudo_rating($d['name']);
        $full  = (int)floor($rating);
        $half  = ((float)$rating - $full) >= 0.5 ? 1 : 0;
        $empty = 5 - $full - $half;
      ?>
      <div class="bg-soft-cream rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300">
        <!-- Card image + badge -->
        <div class="relative overflow-hidden rounded-2xl mb-6">
          <img src="<?= e(img_src($d['image_url'])) ?>" alt="<?= e($d['name']) ?>" class="w-full h-48 object-cover">
          <div class="absolute top-4 right-4 <?= $badgeClass ?> text-white px-3 py-1 rounded-full text-sm font-semibold">
            <?= $badge ?>
          </div>
        </div>

        <!-- Card content -->
        <h4 class="text-xl font-poppins font-bold text-charcoal"><?= e($d['name']) ?></h4>
        <p class="text-sm text-gray-600 leading-relaxed mt-1"><?= e(short_desc($d)) ?></p>

        <!-- Rating -->
        <div class="flex items-center gap-2 mt-2">
          <div class="text-yellow-400">
            <?php for($i=0;$i<$full;$i++): ?><i class="fas fa-star"></i><?php endfor; ?>
            <?php if($half): ?><i class="fas fa-star-half-alt"></i><?php endif; ?>
            <?php for($i=0;$i<$empty;$i++): ?><i class="far fa-star"></i><?php endfor; ?>
          </div>
          <span class="text-xs text-gray-500">(<?= $rating ?>)</span>
        </div>

        <!-- Price + CTA -->
        <div class="flex justify-between items-center mt-4">
          <span class="text-2xl font-bold text-deep-teal">$<?= number_format($d['price'],2) ?></span>

          <!-- Direct customize CTA (routes to customize page) -->
          <form method="post" action="<?= BASE_URL ?>user/cart.php" class="m-0">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="drink_id" value="<?= (int)$d['id'] ?>">
            
            <a href="<?= BASE_URL ?>pages/customize.php?id=<?= (int)$d['id'] ?>"
               class="bg-muted-coral text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition-all duration-300">
              Customize
            </a>
          </form>
        </div>
      </div>
      <?php endforeach; else: ?>

        <!-- Fallback cards when no specials exist -->
        <?php for($i=0;$i<3;$i++): ?>
          <div class="bg-soft-cream rounded-3xl p-6 shadow-lg opacity-80">
            <div class="relative overflow-hidden rounded-2xl mb-6">
              <img src="<?= BASE_URL ?>assets/img/placeholder.jpg" alt="Coming soon" class="w-full h-48 object-cover">
              <div class="absolute top-4 right-4 bg-gray-500 text-white px-3 py-1 rounded-full text-sm font-semibold">Coming Soon</div>
            </div>
            <h4 class="text-xl font-poppins font-bold text-charcoal mb-1">New Drink</h4>
            <p class="text-sm text-gray-600">Blended fresh to order. Great taste, great energy.</p>
            <div class="flex items-center gap-2 mt-2">
              <div class="text-yellow-400"><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i></div>
              <span class="text-xs text-gray-500">(—)</span>
            </div>
            <div class="flex justify-between items-center mt-4">
              <span class="text-2xl font-bold text-deep-teal">$0.00</span>
              <a class="brand-btn opacity-70 pointer-events-none">Add to Cart</a>
            </div>
          </div>
        <?php endfor; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- "WHY CHOOSE BLENDUP" (features) -->
<section class="py-20 bg-soft-cream">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h3 class="text-3xl md:text-4xl font-poppins font-bold text-center text-charcoal">Why Choose BlendUp?</h3>
    <div class="mt-12 grid md:grid-cols-3 gap-8">
      <div class="text-center bg-soft-cream rounded-3xl ">
        <div class="mx-auto w-14 h-14 rounded-full bg-olive-green/15 flex items-center justify-center mb-4">
          <i class="fas fa-leaf text-olive-green"></i>
        </div>
        <h4 class="font-poppins font-bold text-lg mb-1">100% Natural</h4>
        <p class="text-gray-600 text-sm">No artificial flavors, colors, or preservatives. Just pure, natural goodness.</p>
      </div>
      <div class="text-center bg-soft-cream rounded-3xl ">
        <div class="mx-auto w-14 h-14 rounded-full bg-muted-coral/15 flex items-center justify-center mb-4">
          <i class="fas fa-shipping-fast text-muted-coral"></i>
        </div>
        <h4 class="font-poppins font-bold text-lg mb-1">Fast Delivery</h4>
        <p class="text-gray-600 text-sm">Fresh smoothies delivered to your doorstep within 30 minutes.</p>
      </div>
      <div class="text-center bg-soft-cream rounded-3xl">
        <div class="mx-auto w-14 h-14 rounded-full bg-deep-teal/15 flex items-center justify-center mb-4">
          <i class="fas fa-heart text-deep-teal"></i>
        </div>
        <h4 class="font-poppins font-bold text-lg mb-1">Made with Love</h4>
        <p class="text-gray-600 text-sm">Each drink is crafted carefully by our passionate team.</p>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
