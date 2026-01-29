<?php
require_once __DIR__ . '/../includes/functions.php';
if (is_logged_in()) redirect('/blendupfinal/index.php');

$error = '';
$DEBUG = isset($_GET['debug']) ? true : false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $mode     = $_POST['mode'] ?? 'login';
  $emailRaw = $_POST['email'] ?? '';
  $email    = strtolower(trim($emailRaw));          // normalize email
  $password = $_POST['password'] ?? '';

  if ($mode === 'register') {
    $name    = trim($_POST['name'] ?? 'Customer');  // now actually provided by the form
    $confirm = $_POST['confirm'] ?? '';

    if ($password !== $confirm) {
      $error = 'Passwords do not match.';
    } elseif (find_user_by_email($email)) {
      $error = 'Email already registered.';
    } else {
      create_user($name, $email, $password);
      $user = find_user_by_email($email);
      $_SESSION['user'] = [
        'id'=>$user['id'],'name'=>$user['name'],'email'=>$user['email'],'role'=>$user['role'] ?? 'customer'
      ];
      redirect($_GET['next'] ?? '/blendupfinal/index.php');
    }
  } else {
    $user = find_user_by_email($email);

    if ($DEBUG) {
      $found = $user ? 'yes' : 'no';
      $ver   = ($user && password_verify($password, $user['password_hash'])) ? 'yes' : 'no';
      echo "<pre>DEBUG:\nemail_submitted: {$email}\nuser_found: {$found}\npassword_verify: {$ver}\n</pre>";
    }

    if (!$user || !password_verify($password, $user['password_hash'])) {
      $error = 'Invalid credentials.';
    } else {
      $_SESSION['user'] = [
        'id'=>$user['id'],'name'=>$user['name'],'email'=>$user['email'],'role'=>$user['role'] ?? 'customer'
      ];
      redirect($_GET['next'] ?? '/blendupfinal/index.php');
    }
  }
}

$title='BlendUp — Login / Register';
require __DIR__ . '/../includes/header.php';
?>

<!-- Split layout -->
<div class="grid lg:grid-cols-2 min-h-screen">
  <!-- Left photo -->
  <div class="relative hidden lg:block">
    <img src="https://images.stockcake.com/public/a/e/c/aec92c57-ac9b-4fdf-8fec-7113a63c1a86_large/juice-bar-social-stockcake.jpg"
         alt="" class="absolute inset-0 w-full h-full object-cover">
    <div class="absolute top-4 left-4 bg-white/90 text-[#280a3e] font-poppins font-bold px-3 py-1 rounded-full shadow">
      BlendUp
    </div>
  </div>

  <!-- Right card -->
  <div class="bg-soft-cream flex items-center justify-center py-14 px-4">
    <div class="w-full max-w-xl">
      <div class="bg-white rounded-[24px] shadow-xl border border-[#efe3bf] p-8 md:p-10">
        <h1 class="font-poppins text-3xl font-bold text-[#280a3e]">Welcome back!</h1>
        <p class="mt-1 text-[15px] text-charcoal/80">Login or create an account to order.</p>

        <?php if($error): ?>
          <div class="mt-5 p-3 rounded-2xl bg-red-50 text-red-700 text-sm"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" class="mt-6 space-y-5">
          <input type="hidden" id="mode" name="mode" value="login">

          <div>
            <label class="block text-sm font-semibold text-[#280a3e] mb-2">Email</label>
            <input
              name="email" type="email" autocomplete="email" required
              value="<?= e($_POST['email'] ?? '') ?>"
              class="w-full rounded-2xl border border-gray-200 focus:ring-2 focus:ring-deep-teal focus:border-transparent px-4 py-3 placeholder:text-gray-400"
              placeholder="you@example.com">
          </div>

          <!-- NEW: Name (for Register) -->
          <div>
            <label class="block text-sm font-semibold text-[#280a3e] mb-2">
              Full Name <span class="text-xs text-gray-500 font-normal">(for Register)</span>
            </label>
            <input
              name="name" type="text" autocomplete="name"
              value="<?= e($_POST['name'] ?? '') ?>"
              class="w-full rounded-2xl border border-gray-200 focus:ring-2 focus:ring-deep-teal focus:border-transparent px-4 py-3"
              placeholder="Your name">
          </div>

          <div>
            <label class="block text-sm font-semibold text-[#280a3e] mb-2">Password</label>
            <input
              name="password" type="password" autocomplete="current-password" required
              class="w-full rounded-2xl border border-gray-200 focus:ring-2 focus:ring-deep-teal focus:border-transparent px-4 py-3">
          </div>

          <div>
            <div class="flex items-baseline justify-between">
              <label class="block text-sm font-semibold text-[#280a3e] mb-2">
                Confirm Password <span class="text-xs text-gray-500 font-normal">(for Register)</span>
              </label>
              <a href="#" class="text-sm text-olive-green hover:underline">Forgot Password?</a>
            </div>
            <input
              name="confirm" type="password" autocomplete="new-password"
              class="w-full rounded-2xl border border-gray-200 focus:ring-2 focus:ring-deep-teal focus:border-transparent px-4 py-3">
          </div>

          <!-- Buttons -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
            <button type="submit" data-mode="login"
              class="h-12 rounded-2xl bg-deep-teal text-white font-semibold hover:bg-opacity-90 transition">
              Login
            </button>
            <button type="submit" data-mode="register"
              class="h-12 rounded-2xl border-2 border-olive-green text-olive-green font-semibold hover:bg-olive-green hover:text-white transition">
              Register
            </button>
          </div>

          <div class="pt-2">
            <a href="<?= BASE_URL ?>index.php" class="text-sm text-[#280a3e] hover:underline">← Back to Home</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  // Decide login vs register by which button is clicked
  document.querySelectorAll('button[data-mode]').forEach(btn => {
    btn.addEventListener('click', (ev) => {
      document.getElementById('mode').value = ev.currentTarget.dataset.mode;
    });
  });
</script>


