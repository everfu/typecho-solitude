<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
require_once __DIR__ . '/core/Theme.php';
require_once __DIR__ . '/core/Settings.php';
function themeConfig($form) { \Solitude\Settings::form($form); }
function themeFields($layout) { \Solitude\Settings::fields($layout); }
