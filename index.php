<?php
if (!file_exists(__DIR__ . '/install.lock')) { header('Location: /install.php'); exit; }

/**
 * CakePHP Clone - Root Entry Point Fallback
 */

require_once __DIR__ . '/webroot/index.php';
