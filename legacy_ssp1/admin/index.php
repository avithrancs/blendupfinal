<?php

require_once __DIR__ . '/../includes/functions.php';

// Security check: only admins can access this page
// If not admin, redirect to homepage
if (!is_admin()) redirect('/blendupfinal/index.php');

// page title
$title = 'Admin — BlendUp';

// Include shared header (HTML head, navbar, etc.)
require __DIR__ . '/../includes/header.php';
?>

<!-- Main admin dashboard layout -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
  <!-- Page heading -->
  <h1 class="font-poppins text-2xl font-bold">Admin Panel — BlendUp</h1>

  <!-- Admin quick navigation grid -->
  <div class="grid sm:grid-cols-2 gap-6">
    
    <!-- Link to drinks management page -->
    <a href="/blendupfinal/admin/manage_drinks.php" class="block p-6 rounded-3xl bg-white shadow hover:shadow-lg">
      <h3 class="font-poppins font-bold text-lg">Manage Drinks</h3>
      <p class="text-sm text-gray-600">Create, update, delete drinks</p>
    </a>

    <!-- Link to orders management page -->
    <a href="/blendupfinal/admin/manage_orders.php" class="block p-6 rounded-3xl bg-white shadow hover:shadow-lg">
      <h3 class="font-poppins font-bold text-lg">Manage Orders</h3>
      <p class="text-sm text-gray-600">View and update order status</p>
    </a>
  </div>
</main>

<?php 
// Include footer 
require __DIR__ . '/../includes/footer.php'; 
?>
