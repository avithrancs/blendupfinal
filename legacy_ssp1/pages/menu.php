<?php

// Setup: load helpers + page meta

require_once __DIR__ . '/../includes/functions.php';
$title  = 'BlendUp - Fresh Menu';
$active = 'menu';
require __DIR__ . '/../includes/header.php';


// Inputs + data: search & category filters

$q   = trim($_GET['q'] ?? '');
$cat = $_GET['cat'] ?? 'all';
$drinks = get_drinks($q, $cat);


// View-only helpers (no DB writes)
// - img_src: normalize local/absolute image paths, fall back to placeholder
// - pseudo_rating
// - badge_for
// - short_desc

if (!function_exists('img_src')) {
  function img_src($path) {
    if (!$path) return BASE_URL . 'assets/img/placeholder.jpg';
    if (preg_match('~^https?://~i', $path)) return $path;
    return BASE_URL . ltrim($path, '/');
  }
}
function pseudo_rating(string $name): string {
  
  $seed = crc32(strtolower($name));
  $v = 4.3 + (($seed % 70) / 100); 
  return number_format($v, 1);
}
function badge_for(array $d): array {
  $name = strtolower($d['name'] ?? '');
  $cat  = strtolower($d['category'] ?? '');
  if (!empty($d['is_featured']))           return ['Popular','bg-olive-green'];
  if (str_contains($name,'shot'))          return ['Shot','bg-deep-teal'];
  if (str_contains($name,'refresh'))       return ['Refreshing','bg-olive-green'];
  if ($cat === 'seasonal')                 return ['New','bg-muted-coral'];
  if (str_contains($name,'detox') || str_contains($name,'green')) return ['Detox','bg-olive-green'];
  if (str_contains($name,'orange'))        return ['Classic','bg-deep-teal'];
  return ['', ''];
}
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
?>

<!-- Hero -->
<section class="bg-gradient-to-r from-deep-teal to-olive-green text-white py-16 text-center">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h2 class="text-4xl md:text-5xl font-poppins font-bold mb-2">Fresh Menu</h2>
    <p class="text-xl text-gray-100 max-w-2xl mx-auto">Discover our carefully crafted collection of fresh juices, smoothies, and seasonal specials.</p>
  </div>
</section>

<!-- Filters: search + category chips -->
<section class="py-12 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <form class="flex flex-col lg:flex-row gap-6 items-center justify-between w-full">
      <!-- Search box -->
      <div class="w-full lg:w-1/2 relative">
        <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
        <input name="q" value="<?= e($q) ?>" class="w-full pl-12 pr-4 py-4 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-deep-teal" placeholder="Search for your favorite drink..." />
      </div>

      <!-- Category chips -->
      <div class="flex flex-wrap gap-3">
        <?php
          $cats = ['all'=>'All','Juices'=>'Fresh Juices','Smoothies'=>'Smoothies','Seasonal'=>'Seasonal'];
          foreach ($cats as $k=>$label):
            $activeBtn = ($cat===$k) ? 'bg-deep-teal text-white' : 'bg-gray-100 text-charcoal hover:bg-deep-teal hover:text-white';
        ?>
        <a href="?q=<?= urlencode($q) ?>&cat=<?= urlencode($k) ?>" class="px-6 py-3 rounded-full text-sm font-semibold transition-all duration-300 <?= $activeBtn ?>">
          <?= $label ?>
        </a>
        <?php endforeach; ?>
        <button class="px-6 py-3 rounded-full text-sm font-semibold border" type="submit">Apply</button>
      </div>
    </form>
  </div>
</section>

<!-- Cards grid -->
<section class="py-12 bg-soft-cream">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php foreach($drinks as $d):
      [$badge,$badgeCls] = badge_for($d);
      $rating = pseudo_rating($d['name']);
      $full  = (int)floor($rating);
      $half  = ((float)$rating - $full) >= 0.5 ? 1 : 0;
      $empty = 5 - $full - $half;
    ?>
    <div class="bg-white rounded-3xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300">

      <!-- Image + optional badge -->
      <div class="relative overflow-hidden rounded-2xl mb-6 aspect-[4/3] sm:aspect-[16/9]">
        <img
          src="<?= e(img_src($d['image_url'])) ?>"
          alt="<?= e($d['name']) ?>"
          class="absolute inset-0 w-full h-full object-cover"
          loading="lazy"
        >
        <?php if($badge): ?>
          <div class="absolute top-4 right-4 <?= $badgeCls ?> text-white px-3 py-1 rounded-full text-sm font-semibold"><?= $badge ?></div>
        <?php endif; ?>
      </div>

      <!-- Content: title, desc, rating, price + CTA -->
      <div class="space-y-3">
        <h4 class="text-xl font-poppins font-bold text-charcoal"><?= e($d['name']) ?></h4>

        <!-- Blurb -->
        <p class="text-sm text-gray-600 leading-relaxed"><?= e(short_desc($d)) ?></p>

        <!-- Rating row -->
        <div class="flex items-center gap-2">
          <div class="text-yellow-400">
            <?php for($i=0;$i<$full;$i++): ?><i class="fas fa-star"></i><?php endfor; ?>
            <?php if($half): ?><i class="fas fa-star-half-alt"></i><?php endif; ?>
            <?php for($i=0;$i<$empty;$i++): ?><i class="far fa-star"></i><?php endfor; ?>
          </div>
          <span class="text-xs text-gray-500">(<?= $rating ?>)</span>
        </div>

        <!-- Price + Customize link -->
        <div class="flex justify-between items-center pt-2">
          <span class="text-2xl font-bold text-deep-teal">$<?= number_format($d['price'],2) ?></span>

          <a href="<?= BASE_URL ?>pages/customize.php?id=<?= (int)$d['id'] ?>"
             class="bg-muted-coral text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition-all duration-300">
            Customize
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
