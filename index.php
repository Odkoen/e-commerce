<?php

define("ROOTPATH", dirname(__FILE__));


define("APPPATH", ROOTPATH."/php/");



$protocol = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] && $_SERVER["HTTPS"] != "off"
    ? "https://" : "http://";


$site_url = $protocol
    . $_SERVER["HTTP_HOST"]
    . (dirname($_SERVER["SCRIPT_NAME"]) == DIRECTORY_SEPARATOR ? "" : "/")
    . trim(str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"])), "/");

define("SITEURL", $site_url);

$config['app_url'] = SITEURL."/php/";


require_once ROOTPATH . '/includes/classes/AltoRouter.php';

$router = new AltoRouter();
 
$bp = trim(str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"])), "/");
$router->setBasePath($bp ? "/".$bp : "");

$router->map('GET|POST','/', 'home.php');
$router->map('GET|POST','/home/[a:lang]?/?', 'home.php');
$router->map('GET|POST','/home/[a:lang]?/[a:country]?/?', 'home.php');
$router->map('GET|POST','/signup/?', 'signup.php');
$router->map('GET|POST','/index1/?', 'index1.php');
$router->map('GET|POST','/index2/?', 'index2.php');
$router->map('GET|POST','/login/?', 'login.php');
$router->map('GET|POST','/logout/?', 'logout.php');
$router->map('GET|POST','/message/?', 'message.php');
$router->map('GET|POST','/forgot/?', 'forgot.php');
$router->map('GET|POST','/dashboard/?', 'dashboard.php');
$router->map('GET|POST','/myads/[*:page]?/?', 'ad-my.php');
$router->map('GET|POST','/pending/[*:page]?/?', 'ad-pending.php');
$router->map('GET|POST','/expire/[*:page]?/?', 'ad-expire.php');
$router->map('GET|POST','/favourite/[*:page]?/?', 'ad-favourite.php');
$router->map('GET|POST','/hidden/[*:page]?/?', 'ad-hidden.php');
$router->map('GET|POST','/resubmission/[*:page]?/?', 'ad-resubmission.php');
$router->map('GET|POST','/transaction/?', 'transaction.php');
$router->map('GET|POST','/account-setting/?', 'account-setting.php');
$router->map('GET|POST','/report/?', 'report.php');
$router->map('GET|POST','/contact/?', 'contact.php');
$router->map('GET|POST','/sitemap/?', 'sitemap.php');
$router->map('GET|POST','/countries/?', 'countries.php');
$router->map('GET|POST','/faq/?', 'faq.php');
$router->map('GET|POST','/feedback/?', 'feedback.php');
$router->map('GET|POST','/test/?', 'test.php');


$router->map('GET|POST','/profile/[*:username]?/[*:page]?/?','profile.php');
$router->map('GET|POST','/ad/[i:id]?/[*:slug]?/?', 'ad-detail.php');
$router->map('GET|POST','/post-ad/[a:lang]?/[a:country]?/[a:action]?/?', 'ad-post.php');
$router->map('GET|POST','/edit-ad/[i:id]?/[a:lang]?/[a:country]?/[a:action]?/?', 'ad-edit.php');
$router->map('GET|POST','/listing/?', 'listing.php');
$router->map('GET|POST','/category/[*:cat]?/[*:subcat]?/?', 'listing.php');
$router->map('GET|POST','/sub-category/[*:subcat]?/[*:slug]?/?', 'listing.php');
$router->map('GET|POST','/city/[i:city]?/[*:slug]?/?', 'listing.php');
$router->map('GET|POST','/keywords/[*:keywords]?/?', 'listing.php');
$router->map('GET|POST','/page/[*:id]?/?', 'html.php');
$router->map('GET|POST','/membership/[a:change_plan]?/?', 'membership.php');
$router->map('GET|POST','/ipn/[a:i]?/[*:access_token]?/?', 'ipn.php');
$router->map('GET|POST','/payment/[*:access_token]?/[a:i]?/[a:status]?/?', 'payment.php');
$router->map('GET','/sitemap.xml/?', 'xml.php');
$router->map('GET|POST','/testimonials/?', 'testimonials.php');
$router->map('GET|POST','/blog/?', 'blog.php');
$router->map('GET|POST','/blog/category/[*:keyword]/?', 'blog-category.php');
$router->map('GET|POST','/blog/author/[*:keyword]/?', 'blog-author.php');
$router->map('GET|POST','/blog/[i:id]?/[*:slug]?/?', 'blog-single.php');


$match=$router->match();
if($match) {
  
    $_GET = array_merge($match['params'],$_GET);

    require_once ROOTPATH . '/includes/config.php';

    if(!isset($config['installed']))
    {
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
        $site_url = $protocol . $_SERVER['HTTP_HOST'] . str_replace ("index.php", "", $_SERVER['PHP_SELF']);
        header("Location: ".$site_url."install/");
        exit;
    }

    require_once ROOTPATH . '/includes/sql_builder/idiorm.php';
    require_once ROOTPATH . '/includes/db.php';
    require_once ROOTPATH . '/includes/classes/class.template_engine.php';
    require_once ROOTPATH . '/includes/classes/class.country.php';
    require_once ROOTPATH . '/includes/functions/func.global.php';
    require_once ROOTPATH . '/includes/lib/password.php';
    require_once ROOTPATH . '/includes/functions/func.users.php';
    require_once ROOTPATH . '/includes/functions/func.sqlquery.php';
    require_once ROOTPATH . '/includes/classes/GoogleTranslate.php';

    if(isset($_GET['lang'])) {
        if ($_GET['lang'] != ""){
            change_user_lang($_GET['lang']);
        }
    }

    require_once ROOTPATH . '/includes/lang/lang_'.$config['lang'].'.php';
    require_once ROOTPATH . '/includes/seo-url.php';

    sec_session_start();
    $mysqli = db_connect();

    require APPPATH.$match['target'];


}
else {
	
   header("HTTP/1.0 404 Not Found");
   require APPPATH.'404.php';
}
?>