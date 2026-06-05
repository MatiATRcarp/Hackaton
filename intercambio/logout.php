<?php
require_once 'sesion.php';

session_destroy();

header('Location: login.php?msg=logout');
exit;
