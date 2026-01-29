<?php
// Ensure session exists (needed for cart count, auth checks)
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <!-- Dynamic page title -->
  <title><?= e($title ?? 'BlendUp') ?></title>

  <!-- Compiled Tailwind CSS -->
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/output.css">
  <!-- Icons (Font Awesome) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<!-- Global body styles (theme background + base font) -->
<body class="bg-soft-cream font-open-sans">

<!-- Sticky site header / navbar-->
<header class="bg-white shadow-lg sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center py-4">

      <!-- Brand: logo + name + tagline -->
      <div class="flex items-center space-x-2">
        <div class="w-10 h-10 bg-deep-teal rounded-full flex items-center justify-center">
          <i class="fas fa-blender text-white text-lg"></i>
        </div>
        <div>
          <a href="/blendupfinal/index.php" class="text-2xl font-poppins font-bold text-deep-teal">BlendUp</a>
          <p class="text-xs text-charcoal font-light">Sip Fresh. Live Fresh.</p>
        </div>
      </div>

      <!-- Desktop navigation (active tab highlighted) -->
      <nav class="hidden md:flex items-center space-x-6">
        <a href="/blendupfinal/index.php" class="<?= ($active ?? '')==='home' ? 'text-deep-teal font-semibold border-b-2 border-deep-teal pb-1' : 'text-charcoal hover:text-deep-teal' ?>">Home</a>
        <a href="/blendupfinal/pages/menu.php" class="<?= ($active ?? '')==='menu' ? 'text-deep-teal font-semibold border-b-2 border-deep-teal pb-1' : 'text-charcoal hover:text-deep-teal' ?>">Menu</a>

        <!-- Cart link with dynamic badge (sum of cart quantities) -->
        <a href="/blendupfinal/user/cart.php" class="text-charcoal hover:text-deep-teal relative">
          <i class="fas fa-shopping-cart"></i>
          <span class="ml-1">Cart</span>
          <?php $count = array_sum(array_column($_SESSION['cart'] ?? [], 'quantity')); ?>
          <span class="absolute -top-2 -right-2 bg-muted-coral text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
            <?= $count ?: 0 ?>
          </span>
        </a>

        <!-- Auth-aware links (Admin, Login/Logout) -->
        <?php if(is_logged_in()): ?>
          <?php if(is_admin()): ?>
            <a class="text-charcoal hover:text-deep-teal" href="/blendupfinal/admin/index.php">Admin</a>
          <?php endif; ?>
          <a class="brand-btn" href="/blendupfinal/auth/logout.php">Logout</a>
        <?php else: ?>
          <a class="brand-btn" href="/blendupfinal/auth/login.php">Login</a>
        <?php endif; ?>
      </nav>

      <!-- Mobile menu toggle button -->
      <button class="md:hidden text-deep-teal" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
        <i class="fas fa-bars text-xl"></i>
      </button>
    </div>

    <!-- Mobile navigation (hidden by default; toggled via button) -->
    <div id="mobile-menu" class="hidden md:hidden pb-4">
      <div class="flex flex-col space-y-3">
        <a href="/blendupfinal/index.php" class="text-charcoal">Home</a>
        <a href="/blendupfinal/pages/menu.php" class="text-charcoal">Menu</a>
        <a href="/blendupfinal/user/cart.php" class="text-charcoal">Cart</a>

        <!-- Auth-aware items (mobile) -->
        <?php if(is_logged_in()): ?>
          <?php if(is_admin()): ?>
            <a class="text-charcoal" href="/blendupfinal/admin/index.php">Admin</a>
          <?php endif; ?>
          <a class="text-charcoal" href="/blendupfinal/auth/logout.php">Logout</a>
        <?php else: ?>
          <a href="/blendupfinal/auth/login.php" class="text-charcoal">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</header>
