<?php
// Path to root directory of app.
require_once('../includes/config.php');
require_once('../includes/sql_builder/idiorm.php');
require_once('../includes/db.php');
require_once('../includes/functions/func.global.php');
require_once('../includes/functions/func.admin.php');
require_once('../includes/functions/func.users.php');
require_once('../includes/functions/func.sqlquery.php');
require_once('../includes/lang/lang_'.$config['lang'].'.php');

$mysqli = db_connect();
admin_session_start();
checkloggedadmin();

define("ROOTPATH", dirname(__DIR__));
define("ADMINPATH", __DIR__);

// Check if SSL enabled
$ssl = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] && $_SERVER["HTTPS"] != "off"
    ? true
    : false;
define("SSL_ENABLED", $ssl);

// Define SITEURL
$admin_url = (SSL_ENABLED ? "https" : "http")
    . "://"
    . $_SERVER["SERVER_NAME"]
    . (dirname($_SERVER["SCRIPT_NAME"]) == DIRECTORY_SEPARATOR ? "" : "/")
    . trim(str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"])), "/");

define("ADMINURL", $admin_url);
include('header.php');

