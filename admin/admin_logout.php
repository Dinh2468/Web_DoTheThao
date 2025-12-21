<?php
session_start();

/* XÓA TOÀN BỘ SESSION */
$_SESSION = [];
session_destroy();

/* QUAY VỀ TRANG LOGIN (CÙNG THƯ MỤC) */
header("Location: admin_login.php");
exit;
