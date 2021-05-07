<?php
ignore_user_abort(1);

if ($config['version'] < "9.1") {
    update_option("non_active_msg",'1');
    update_option("non_active_allow",'0');
    update_option("cookie_link",'');
    update_option("cookie_consent",'1');
    update_option('socket_host', 'localhost');
    update_option('socket_port', '9300');
    update_option('listing_view', 'list');
    update_option('map_type', 'openstreet');
}
if ($config['version'] < "9") {

    /*$sql = "INSERT INTO `".$config['db']['pre']."payments` (`payment_id`, `payment_install`, `payment_title`, `payment_folder`, `payment_desc`) VALUES (NULL, '0', 'Paytm', 'paytm', NULL);";
    mysqli_query($mysqli,$sql);*/

    $sql = "ALTER TABLE `".$config['db']['pre']."messages` ADD `post_id` INT(11) NULL DEFAULT NULL AFTER `message_type`";
    mysqli_query($mysqli,$sql);

    // add default values for new options
    update_option("blog_enable",'1');
    update_option("blog_banner",'1');
    update_option("show_blog_home",'1');
    update_option("blog_comment_enable",'1');
    update_option("blog_comment_approval",'2');
    update_option("blog_comment_user",'1');
    update_option("testimonials_enable",'1');
    update_option("show_testimonials_blog",'1');
    update_option("show_testimonials_home",'1');

    // create database tables
    $sql = "CREATE TABLE IF NOT EXISTS `".$config['db']['pre']."blog` (
          `id` int(10) NOT NULL AUTO_INCREMENT,
          `author` int(10) DEFAULT NULL,
          `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
          `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
          `image` varchar(255) DEFAULT NULL,
          `tags` text CHARACTER SET utf32 COLLATE utf32_unicode_ci,
          `status` enum('publish','pending') NOT NULL DEFAULT 'publish',
          `created_at` datetime DEFAULT NULL,
          `updated_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    mysqli_query($mysqli,$sql);

    $sql = "CREATE TABLE IF NOT EXISTS `".$config['db']['pre']."blog_categories` (
          `id` int(10) NOT NULL AUTO_INCREMENT,
          `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
          `slug` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
          `position` int(10) NOT NULL DEFAULT '0',
          `active` enum('0','1') NOT NULL DEFAULT '1',
          PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    mysqli_query($mysqli,$sql);

    $sql = "CREATE TABLE IF NOT EXISTS `".$config['db']['pre']."blog_cat_relation` (
          `id` int(10) NOT NULL AUTO_INCREMENT,
          `blog_id` int(10) DEFAULT NULL,
          `category_id` int(10) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    mysqli_query($mysqli,$sql);

    $sql = "CREATE TABLE IF NOT EXISTS `".$config['db']['pre']."blog_comment` (
          `id` int(10) NOT NULL AUTO_INCREMENT,
          `blog_id` int(10) DEFAULT NULL,
          `user_id` int(10) DEFAULT NULL,
          `is_admin` enum('0','1') NOT NULL DEFAULT '0',
          `name` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
          `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
          `comment` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
          `created_at` datetime DEFAULT NULL,
          `active` enum('0','1') NOT NULL DEFAULT '1',
          `parent` int(10) NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    mysqli_query($mysqli,$sql);

    $sql = "CREATE TABLE IF NOT EXISTS `".$config['db']['pre']."testimonials` (
          `id` int(10) NOT NULL AUTO_INCREMENT,
          `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
          `designation` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
          `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
          `image` varchar(100) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    mysqli_query($mysqli,$sql);

    // insert demo data
    $sql = "INSERT INTO `".$config['db']['pre']."blog` (`author`, `title`, `description`, `image`, `tags`, `status`, `created_at`, `updated_at`) VALUES (1, 'First Blog', '<p>Consectetur adipisicing elitsed do eiusmod tempor incididunt ut labore et dolore magna aliqua Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commo do consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla paria tur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.<blockquote>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla paria tur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</blockquote><p>Elitsed do eiusmod tempor incididunt ut labore et dolore magna aliqua Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commo do consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla paria tur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim.</p><p></p></p>\n', NULL, 'travel fun, love', 'publish', '2020-01-15 23:05:15', '2020-01-17 19:12:18')";
    mysqli_query($mysqli,$sql);

    $sql = "INSERT INTO `".$config['db']['pre']."testimonials` (`name`, `designation`, `content`, `image`) VALUES ('Tony Stark', 'Social Marketing', 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla paria tur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', NULL)";
    mysqli_query($mysqli,$sql);

    // create required folders and files
    $srcfile = '../storage/products/default.png';
    $dstfile = '../storage/blog/';
    if(!is_dir($dstfile))
        mkdir ( $dstfile, 0755, true );
    @copy($srcfile, $dstfile.'default.png');

    $srcfile = '../storage/profile/default_user.png';
    $dstfile = '../storage/testimonials/';
    if(!is_dir($dstfile))
        mkdir ( $dstfile, 0755, true );
    @copy($srcfile, $dstfile.'default_user.png');


}

if ($config['version'] <= "8.3") {

    echo "DROP TABLE push_notification Table...  \t\t";
    $q = "DROP TABLE IF EXISTS `".$config['db']['pre']."push_notification`";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "DROP TABLE push_notification success<br>";

    echo "CREATE TABLE push_notification Table...  \t\t";
    $q = "CREATE TABLE `".$config['db']['pre']."push_notification` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) DEFAULT NULL,
  `sender_name` varchar(255) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_title` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `recd` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "CREATE TABLE push_notification success<br>";

   /*-----------------------------------------------------*/

    echo "DROP TABLE firebase_device_token Table...  \t\t";
    $q = "DROP TABLE IF EXISTS `".$config['db']['pre']."firebase_device_token`";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "DROP TABLE firebase_device_token success<br>";

    echo "CREATE TABLE firebase_device_token Table...  \t\t";
    $q = "CREATE TABLE `".$config['db']['pre']."firebase_device_token` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `device_id` varchar(55) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `token` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    @mysqli_query($mysqli, $q) OR error(mysqli_error($mysqli));
    echo "CREATE TABLE firebase_device_token success<br>";
}
?>