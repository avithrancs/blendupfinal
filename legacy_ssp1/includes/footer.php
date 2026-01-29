<footer class="bg-charcoal text-white py-14 mt-16">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    
         <!-- Top section: 3 columns -->
         
    <div class="grid md:grid-cols-3 gap-10">

      <!-- Column 1: Brand + tagline + social icons -->
      <div>
        <div class="flex items-center gap-3 mb-4">
          
        <!-- Logo icon -->
          <div class="w-10 h-10 bg-deep-teal rounded-full flex items-center justify-center">
            <i class="fas fa-blender text-white text-lg"></i>
          </div>
          <div>
            <h3 class="text-2xl font-poppins font-bold leading-tight">BlendUp</h3>
            <p class="text-xs text-gray-300">Sip Fresh. Live Fresh.</p>
          </div>
        </div>

        <!-- Brand description -->
        <p class="text-gray-300/90 text-sm leading-relaxed max-w-md">
          Your go-to destination for fresh, healthy, and delicious juices and smoothies.
          We’re committed to bringing you the best quality drinks made from premium ingredients.
        </p>

        <!-- Social media links -->
        <div class="flex items-center gap-4 mt-5">
          <a href="#" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 transition flex items-center justify-center" aria-label="Facebook">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="#" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 transition flex items-center justify-center" aria-label="Instagram">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="#" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 transition flex items-center justify-center" aria-label="Twitter">
            <i class="fab fa-twitter"></i>
          </a>
        </div>
      </div>

      <!-- Column 2: Quick Links -->
      <div>
        <h4 class="font-poppins font-bold mb-4">Quick Links</h4>
        <ul class="space-y-3 text-gray-300">
          <li><a class="hover:text-white" href="<?= BASE_URL ?>pages/menu.php">Menu</a></li>
          <li><a class="hover:text-white" href="<?= BASE_URL ?>index.php#featured">About Us</a></li>
          <li><a class="hover:text-white" href="#">Nutrition</a></li>
          <li><a class="hover:text-white" href="#">Locations</a></li>
        </ul>
      </div>

      <!-- Column 3: Support links -->
      <div>
        <h4 class="font-poppins font-bold mb-4">Support</h4>
        <ul class="space-y-3 text-gray-300">
          <li><a class="hover:text-white" href="#">Contact Us</a></li>
          <li><a class="hover:text-white" href="#">FAQ</a></li>
          <li><a class="hover:text-white" href="#">Privacy Policy</a></li>
          <li><a class="hover:text-white" href="#">Terms &amp; Conditions</a></li>
        </ul>
      </div>
    </div>

    <!-- Bottom bar with divider -->

    <div class="border-t border-white/10 mt-10 pt-6 flex flex-col md:flex-row items-center md:justify-between gap-4">
      
    <!-- Copyright -->
      <p class="text-gray-300 text-sm">© <?= date('Y') ?> BlendUp. All rights reserved.</p>

      <!-- Secondary nav links -->
      <div class="flex items-center gap-6 text-sm text-gray-300">
        <a class="hover:text-white" href="<?= BASE_URL ?>pages/menu.php">Menu</a>
        <a class="hover:text-white" href="<?= BASE_URL ?>user/cart.php">Cart</a>
        <a class="hover:text-white" href="<?= BASE_URL ?>auth/login.php">Login</a>
      </div>
    </div>

  </div>
</footer>
</body>
</html>
