<?php
// Admin ko lagi: uploads/songs/ ma afai rakheko mp3 haru lai list ma halne.
// File ko naam "Artist - Title.mp3" format ma rakhnus (jastai: "Sugam Pokhrel - Aama.mp3").
require 'config.php'; require_login();
if (($_SESSION['role'] ?? '') !== 'admin') die('Admin matra.');
$added = 0;
foreach (glob('uploads/songs/*.{mp3,wav,ogg,m4a}', GLOB_BRACE) as $path) {
    $file = basename($path);
    $st = $conn->prepare('SELECT 1 FROM songs WHERE file=?'); $st->bind_param('s', $file); $st->execute();
    if ($st->get_result()->num_rows) continue;
    $name = pathinfo($file, PATHINFO_FILENAME);
    $parts = explode(' - ', $name, 2);
    $artist = count($parts) == 2 ? trim($parts[0]) : 'Unknown';
    $title = count($parts) == 2 ? trim($parts[1]) : $name;
    $st = $conn->prepare("INSERT INTO songs(title,artist,file,source) VALUES(?,?,?,'local')");
    $st->bind_param('sss', $title, $artist, $file); $st->execute(); $added++;
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Scan — Pulse</title><link rel="stylesheet" href="css/style.css"></head>
<body class="auth"><?php include 'bg.php'; ?>
<div class="box glass"><div class="logo"><span class="dot"></span>Pulse</div>
<h1>Scan complete</h1><p class="ok"><?= (int)$added ?> naya song list ma thapiyo ✅</p>
<p class="sub">Tip: file ko naam "Artist - Title.mp3" format ma rakhnus.</p>
<p class="sub"><a href="index.php">← Back to player</a></p></div></body></html>
