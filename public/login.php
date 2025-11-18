<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../app/controllers/UserController.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ctrl = new UserController();
    $user_id = $ctrl->loginUser($_POST['email'], $_POST['password']);
    if ($user_id) {
        $_SESSION['user_id'] = $user_id;
        echo <<<SCRIPT
<script>
(function(){
  var msg = document.createElement('div');
  msg.textContent = 'Login successful';
  msg.style = 'position:fixed;top:20px;right:20px;padding:12px 18px;background:#28a745;color:#fff;border-radius:4px;box-shadow:0 2px 6px rgba(0,0,0,0.2);font-family:sans-serif;z-index:99999;';
  document.body.appendChild(msg);
  setTimeout(function(){ window.location.href = 'index.php?page=dashboard'; }, 1200);
})();
</script>
SCRIPT;
        exit;
    } else {
        echo <<<SCRIPT
<script>
(function(){
  var msg = document.createElement('div');
  msg.textContent = 'Login failed: invalid email or password';
  msg.style = 'position:fixed;top:20px;right:20px;padding:12px 18px;background:#dc3545;color:#fff;border-radius:4px;box-shadow:0 2px 6px rgba(0,0,0,0.2);font-family:sans-serif;z-index:99999;';
  document.body.appendChild(msg);
  setTimeout(function(){ msg.remove(); }, 5000);
})();
</script>
SCRIPT;
    }
}
?>
<form method="post">
    <input name="email" type="email" required>
    <input name="password" type="password" required>
    <button type="submit">Login</button>
</form>
