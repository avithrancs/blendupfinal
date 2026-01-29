<?php
require_once __DIR__ . '/../includes/functions.php';

// Security check: only admins can access this page
if (!is_admin()) redirect('/blendupfinal/index.php');

$title = 'Admin — Manage Drinks';
require __DIR__ . '/../includes/header.php';

// Handle "edit" mode if ?edit=ID is passed
$editing = null;
if (isset($_GET['edit'])) $editing = get_drink((int)$_GET['edit']);

// Handle form submissions (POST requests)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); // CSRF

  // Delete request
  if (isset($_POST['delete'])) {
    delete_drink((int)$_POST['delete']);
  }
  // Create or Update a drink
  else {
    $id = (int)($_POST['id'] ?? 0);
    save_drink(
      $id,
      trim($_POST['name']),
      (float)$_POST['price'],
      $_POST['category'],
      trim($_POST['image_url']),
      isset($_POST['is_featured']) ? 1 : 0
    );
  }
  redirect('/blendupfinal/admin/manage_drinks.php');
}

// Fetch all drinks
$rows = get_drinks();
?>
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

  <!-- Add/Edit form -->
  <section class="bg-white rounded-3xl p-6 shadow">
    <h3 class="font-poppins text-lg font-bold">
      <?= $editing ? 'Edit Drink' : 'Add Drink' ?>
    </h3>

    <form method="post" class="grid sm:grid-cols-2 gap-4 mt-4">
      <?= csrf_field() ?> <!-- -->
      <input type="hidden" name="id" value="<?= $editing['id'] ?? 0 ?>">

      <input name="name" class="rounded-2xl px-4 py-2 border"
             placeholder="Name"
             value="<?= e($editing['name'] ?? '') ?>" required>

      <input name="price" type="number" step="0.01" class="rounded-2xl px-4 py-2 border"
             placeholder="Price"
             value="<?= e($editing['price'] ?? '') ?>" required>

      <select name="category" class="rounded-2xl px-4 py-2 border">
        <?php foreach (['Smoothies','Juices','Seasonal'] as $c): ?>
          <option <?= (($editing['category'] ?? '') === $c) ? 'selected' : '' ?>>
            <?= $c ?>
          </option>
        <?php endforeach; ?>
      </select>

      <input name="image_url" class="rounded-2xl px-4 py-2 border"
             placeholder="Image URL"
             value="<?= e($editing['image_url'] ?? '') ?>">

      <label class="flex items-center gap-2">
        <input type="checkbox" name="is_featured"
               <?= !empty($editing['is_featured']) ? 'checked' : ''; ?>>
        Featured
      </label>

      <button class="sm:col-span-2 brand-btn">
        <?= $editing ? 'Update' : 'Create' ?>
      </button>
    </form>
  </section>

  <!-- Drinks list -->
  <section class="bg-white rounded-3xl p-6 shadow">
    <h3 class="font-poppins text-lg font-bold">All Drinks</h3>

    <table class="w-full text-sm mt-3">
      <thead>
        <tr class="text-left text-xs text-gray-500">
          <th class="py-2">Name</th>
          <th>Price</th>
          <th>Cat</th>
          <th>Featured</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
        <tr class="border-t">
          <td class="py-2"><?= e($r['name']) ?></td>
          <td>$<?= number_format($r['price'], 2) ?></td>
          <td><?= e($r['category']) ?></td>
          <td><?= $r['is_featured'] ? 'Yes' : 'No' ?></td>
          <td class="space-x-2">
            <a class="text-deep-teal underline" href="?edit=<?= $r['id'] ?>">Edit</a>

            <form method="post" class="inline" onsubmit="return confirm('Delete this drink?')">
              <?= csrf_field() ?> 
              <button name="delete" value="<?= $r['id'] ?>"
                      class="text-red-600 underline">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
