<?php

require_once __DIR__ . '/functions.php';


// Access control: Only allow logged-in users
// If not logged in → redirect to login page
// Also appends "next" query param so user
// returns to the originally requested page after login

if (!is_logged_in()) {
  redirect('/blendupfinal/auth/login.php?next=' . urlencode($_SERVER['REQUEST_URI']));
}
