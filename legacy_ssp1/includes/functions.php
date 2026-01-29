<?php

// load config (DB, session)

require_once __DIR__ . '/../config/config.php';


// Small helpers
// --- CSRF helpers ---
if (!function_exists('csrf_token')) {
  function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
      $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
  }
}
if (!function_exists('csrf_field')) {
  function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="'.htmlspecialchars(csrf_token(),ENT_QUOTES,'UTF-8').'">';
  }
}
if (!function_exists('csrf_check')) {
  function csrf_check(): void {
    $ok = isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
    if (!$ok) {
      http_response_code(403);
      exit('Invalid CSRF token');
    }
  }
}

// HTTP redirect + exit (prevents further output)
function redirect($path) {
  header("Location: $path");
  exit;
}

// HTML-escape output to prevent XSS
function e($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }


# Auth (users)


// Find a user by email (normalized to lowercase, trimmed)
function find_user_by_email($email) {
  global $pdo;
  $email = strtolower(trim($email));                  // normalize
  $st = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
  $st->execute([$email]);
  return $st->fetch();
}


// Create a new user with a bcrypt-hashed password; role defaults to 'customer'
function create_user($name, $email, $password) {
  global $pdo;
  $hash  = password_hash($password, PASSWORD_BCRYPT); // store hashed
  $email = strtolower(trim($email));                  // normalize
  $st = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'customer')");
  $st->execute([$name, $email, $hash]);
  return $pdo->lastInsertId();
}


# Drinks CRUD


// List drinks with optional name search ($q) and category filter ($cat)
function get_drinks($q = null, $cat = null) {
  global $pdo;
  $sql = "SELECT * FROM drinks WHERE 1";
  $params = [];
  if ($q) { $sql .= " AND name LIKE ?"; $params[] = "%$q%"; }
  if ($cat && $cat !== 'all') { $sql .= " AND category = ?"; $params[] = $cat; }
  $sql .= " ORDER BY created_at DESC";
  $st = $pdo->prepare($sql);
  $st->execute($params);
  return $st->fetchAll();
}

// Get a single drink by ID
function get_drink($id) {
  global $pdo;
  $st = $pdo->prepare("SELECT * FROM drinks WHERE id=?");
  $st->execute([$id]);
  return $st->fetch();
}

// Insert or update a drink (decided by $id presence)
function save_drink($id, $name, $price, $category, $image_url, $is_featured) {
  global $pdo;
  if ($id) {
    $st = $pdo->prepare("UPDATE drinks SET name=?, price=?, category=?, image_url=?, is_featured=? WHERE id=?");
    return $st->execute([$name,$price,$category,$image_url,$is_featured,$id]);
  } else {
    $st = $pdo->prepare("INSERT INTO drinks(name, price, category, image_url, is_featured) VALUES(?,?,?,?,?)");
    return $st->execute([$name,$price,$category,$image_url,$is_featured]);
  }
}

// Delete a drink by ID
function delete_drink($id) {
  global $pdo;
  $st = $pdo->prepare("DELETE FROM drinks WHERE id=?");
  return $st->execute([$id]);
}


# Cart (session-based)


// Return current cart from session (array keyed by "drinkId|customizations")
function cart_get() { return $_SESSION['cart'] ?? []; }

/**
 * Add item to cart.
 * $customizations becomes part of the key, so different options are separate lines.
 * $unit_price can override the drink's base price (e.g., when add-ons are included).
 */
function cart_add($drink, $qty = 1, $customizations = '', $unit_price = null) {
  $cart = cart_get();
  $key = (string)$drink['id'] . '|' . $customizations;

  $price = ($unit_price !== null) ? (float)$unit_price : (float)$drink['price'];

  if (!isset($cart[$key])) {
    $cart[$key] = [
      'drink_id'       => $drink['id'],
      'name'           => $drink['name'],
      'price'          => $price,          // price may include add-ons
      'quantity'       => 0,
      'customizations' => $customizations
    ];
  } else {
    // If same key is added with a new computed price, keep the latest price
    $cart[$key]['price'] = $price;
  }
  $cart[$key]['quantity'] += max(1, (int)$qty);
  $_SESSION['cart'] = $cart;
}

// Update quantity for a specific cart key (min 1)
function cart_update($key, $qty) {
  $cart = cart_get();
  if (isset($cart[$key])) {
    $cart[$key]['quantity'] = max(1, (int)$qty);
    $_SESSION['cart'] = $cart;
  }
}

// Remove a line item by key
function cart_remove($key) {
  $cart = cart_get();
  unset($cart[$key]);
  $_SESSION['cart'] = $cart;
}

// Empty the cart
function cart_clear() { unset($_SESSION['cart']); }


# Orders


// Create an order from the current cart (transactional)
// - Inserts into orders and order_items
// - Clears cart on success
function place_order($user_id, $order_type, $payment_method, $address_json) {
  global $pdo;
  $pdo->beginTransaction();
  try {
    // Compute total from session cart
    $cart = cart_get();
    $total = 0;
    foreach ($cart as $item) $total += $item['price'] * $item['quantity'];

    // Insert order header
    $stmt = $pdo->prepare("INSERT INTO orders(user_id, order_type, total, address_json, payment_method) VALUES(?,?,?,?,?)");
    $stmt->execute([$user_id, $order_type, $total, $address_json, $payment_method]);
    $order_id = $pdo->lastInsertId();

    // Insert order items
    $oi = $pdo->prepare("INSERT INTO order_items(order_id, drink_id, drink_name, unit_price, quantity, customizations) VALUES(?,?,?,?,?,?)");
    foreach ($cart as $key=>$item) {
      $oi->execute([$order_id, $item['drink_id'], $item['name'], $item['price'], $item['quantity'], $item['customizations']]);
    }

    // Commit + clear cart
    $pdo->commit();
    cart_clear();
    return $order_id;
  } catch(Exception $e) {
    $pdo->rollBack();
    throw $e;
  }
}

// List orders for a user (most recent first)
function get_user_orders($user_id) {
  global $pdo;
  $st = $pdo->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC");
  $st->execute([$user_id]);
  return $st->fetchAll();
}

// Get items for a specific order
function get_order_items($order_id) {
  global $pdo;
  $st = $pdo->prepare("SELECT * FROM order_items WHERE order_id=?");
  $st->execute([$order_id]);
  return $st->fetchAll();
}
