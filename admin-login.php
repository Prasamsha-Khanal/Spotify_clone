<?php
require 'config.php';
if (($_SESSION['role'] ?? '') === 'admin' && !empty($_SESSION['uid'])) { header('Location: admin.php'); exit; }
$err = '';
if ($w = throttle_locked()) {
    $err = "Dherai choti galat bhayo. $w second pachhi feri try garnus.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username']);
    $st = $conn->prepare("SELECT id,password FROM users WHERE (username=? OR email=?) AND role='admin'");
    $st->bind_param('ss', $u, $u); $st->execute();
    $r = $st->get_result()->fetch_assoc();
    if ($r && password_verify($_POST['password'], $r['password'])) {
        session_regenerate_id(true); throttle_reset();
        $_SESSION['uid'] = $r['id']; $_SESSION['role'] = 'admin';
        track_login($conn, (int)$r['id']);
        header('Location: admin.php'); exit;
    }
    throttle_fail();
    $err = 'Admin username/email ya password milena.';
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin sign in — Pulse</title><link rel="stylesheet" href="css/style.css"></head>
<body class="auth admin-auth"><?php include 'bg.php'; ?>
<form method="post" class="box glass">
  <div class="logo"><span class="dot"></span>Pulse <small>&nbsp;Admin</small></div>
  <span class="tag"><svg class="ic" viewBox="0 0 24 24" style="fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round"><path d="M12 2l8 3v6c0 5-3.5 9-8 11-4.5-2-8-6-8-11V5z"/></svg> Restricted area</span>
  <h1>Admin sign in</h1>
  <p class="sub">Authorised administrators matra.</p>
  <?php if (isset($_GET['created'])) echo '<p class="ok">Admin account bhayo ✓ ahile login garnus.</p>';
  if ($err) echo '<p class="err">' . h($err) . '</p>'; ?>
  <input name="username" placeholder="Admin username ya email" required autofocus>
  <div class="field"><input name="password" id="pw" type="password" placeholder="Password" required>
    <button type="button" class="eye" onclick="var p=document.getElementById('pw');p.type=p.type=='password'?'text':'password'">Show</button></div>
  <button class="btn">Sign in to dashboard</button>
  <p class="sub"><a href="login.php">← Listener login</a></p>
</form></body></html>
