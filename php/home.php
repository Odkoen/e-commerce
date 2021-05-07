<?php
$start = microtime(true);
$limit = 12;

if(isset($match['params']['country'])) {
    if ($match['params']['country'] != ""){
        change_user_country($match['params']['country']);
    }
}

$sortname = check_user_country();

if($latlong = get_lat_long_of_country($sortname)){
    $mapLat     =  $latlong['lat'];
    $mapLong    =  $latlong['lng'];
}else{
    $mapLat     =  get_option("home_map_latitude");
    $mapLong    =  get_option("home_map_longitude");
}

//Get cities

$popular = array();
$count = 1;

$result = ORM::for_table($config['db']['pre'].'cities')
    ->select_many('id','asciiname')
    ->where(array(
        'active' => '1'
    ))
    ->order_by_desc('population')
    ->limit(18)
    ->find_many();
foreach ($result as $info) {
    $id = $info['id'];
    $name = $info['asciiname'];

    $city_count = ORM::for_table($config['db']['pre'].'product')
        ->where('city', $id)
        ->count();

    $popular[$count]['tpl'] =  '<li><a href= "'.$config['site_url'].'/city/'.$id.'/'.$name.'" class="selectme" data-id="'.$id.'" data-name="'.$name.'"><span>'.$name.' ('.$city_count.')</span></a></li>';
    $count++;
}

//Loop for Premium Ads and (featured = 1 or urgent = 1 or highlight = 1)

$item = get_items("","active",true,1,$limit,"id",true,true,"DESC");
$item2 = get_items("","active",false,1,$limit,"id",true);

$category = get_maincategory();
$cat_dropdown = get_categories_dropdown($lang);

$result = ORM::for_table($config['db']['pre'].'catagory_main')
        ->order_by_asc('cat_order')
        ->find_many();
foreach ($result as $info) {
    if($config['lang_code'] != 'en' && $config['userlangsel'] == '1'){
        $maincat = get_category_translation("main",$info['cat_id']);
        $info['cat_name'] = $maincat['title'];
        $info['slug'] = $maincat['slug'];
    }
    $cat[$info['cat_id']]['icon'] = $info['icon'];
    $cat[$info['cat_id']]['main_title'] = $info['cat_name'];
    $cat[$info['cat_id']]['main_id'] = $info['cat_id'];
    $cat[$info['cat_id']]['picture'] = $info['picture'];
    $cat[$info['cat_id']]['catlink'] = $config['site_url'].'category/'.$info['slug'];

    $totalAdsMaincat = get_items_count(false,"active",false,null,$info['cat_id'],true);
    //$totalAdsMaincat = 0;
    $cat[$info['cat_id']]['main_ads_count'] = $totalAdsMaincat;
    $count = 1;

    $result1 = ORM::for_table($config['db']['pre'].'catagory_sub')
        ->where('main_cat_id', $info['cat_id'])
        ->limit(4)
        ->find_many();
    foreach ($result1 as $info1) {
        //$totalads = get_items_count(false,"active",false,$info1['sub_cat_id'],null,true);

        if($config['lang_code'] != 'en' && $config['userlangsel'] == '1'){
            $subcat = get_category_translation("sub",$info1['sub_cat_id']);
            $info1['sub_cat_name'] = $subcat['title'];
            $info1['slug'] = $subcat['slug'];
        }
        $subcatlink = $config['site_url'].'category/'.$info['slug'].'/'.$info1['slug'];

        if($count == 1)
            $cat[$info['cat_id']]['sub_title'] = '<li><a href="'.$subcatlink.'" title="'.$info1['sub_cat_name'].'">'.$info1['sub_cat_name'].'</a></li>';
        else
            $cat[$info['cat_id']]['sub_title'] .= '<li><a href="'.$subcatlink.'" title="'.$info1['sub_cat_name'].'">'.$info1['sub_cat_name'].'</a></li>';

        if($count == 4)
            $cat[$info['cat_id']]['sub_title'] .= '<li><a href="'.$link['SITEMAP'].'" style="color: #6f6f6f;text-decoration: underline;">'.$lang['VIEW_MORE'].'...</a></li>';
        $count++;
    }
}

// get recent blog
$sql = "SELECT
b.*, u.name, u.username, u.image author_pic, GROUP_CONCAT(c.title) categories, GROUP_CONCAT(c.slug) cat_slugs
FROM `".$config['db']['pre']."blog` b
LEFT JOIN `".$config['db']['pre']."admins` u ON u.id = b.author
LEFT JOIN `" . $config['db']['pre'] . "blog_cat_relation` bc ON bc.blog_id = b.id
LEFT JOIN `" . $config['db']['pre'] . "blog_categories` c ON bc.category_id = c.id
WHERE b.status = 'publish' GROUP BY b.id ORDER BY b.created_at DESC LIMIT 3";
$rows = ORM::for_table($config['db']['pre'].'blog')->raw_query($sql)->find_many();
$recent_blog = array();
foreach ($rows as $info) {
    $recent_blog[$info['id']]['id'] = $info['id'];
    $recent_blog[$info['id']]['title'] = $info['title'];
    $recent_blog[$info['id']]['image'] = !empty($info['image'])?$info['image']:'default.png';
    $recent_blog[$info['id']]['description'] = strlimiter(strip_tags(stripslashes($info['description'])),100);
    $recent_blog[$info['id']]['author'] = $info['name'];
    $recent_blog[$info['id']]['author_link'] = $link['BLOG-AUTHOR'].'/'.$info['username'];
    $recent_blog[$info['id']]['author_pic'] = !empty($info['author_pic'])?$info['author_pic']:'default_user.png';
    $recent_blog[$info['id']]['created_at'] = timeAgo($info['created_at']);
    $recent_blog[$info['id']]['link'] = $link['BLOG-SINGLE'].'/'.$info['id'].'/'.create_slug($info['title']);

    $categories = explode(',',$info['categories']);
    $cat_slugs = explode(',',$info['cat_slugs']);
    $arr = array();
    for($i = 0; $i < count($categories); $i++){
        $arr[] = '<a href="'.$link['BLOG-CAT'].'/'.$cat_slugs[$i].'">'.$categories[$i].'</a>';
    }
    $recent_blog[$info['id']]['categories'] = implode(', ',$arr);
}

// get testimonials
$rows = ORM::for_table($config['db']['pre'] . 'testimonials')
    ->order_by_desc('id')
    ->limit(5)
    ->find_many();
$testimonials = array();
foreach ($rows as $row) {
    $testimonials[$row['id']]['id'] = $row['id'];
    $testimonials[$row['id']]['name'] = $row['name'];
    $testimonials[$row['id']]['designation'] = $row['designation'];
    $testimonials[$row['id']]['content'] = $row['content'];
    $testimonials[$row['id']]['image'] = !empty($row['image']) ? $row['image'] : 'default_user.png';
}

// Output to template

if($config['home_page'] == "home-map"){
    $page = new HtmlTemplate ('templates/'.$config['tpl_name'].'/home-map.tpl');
}
else{
    $page = new HtmlTemplate ('templates/'.$config['tpl_name'].'/index.tpl');
}


$page->SetParameter ('OVERALL_HEADER', create_header());
$page->SetLoop ('POPULARCITY',$popular);
$page->SetLoop ('ITEM', $item);
$page->SetLoop ('ITEM2', $item2);
$page->SetParameter('POST_PREMIUM_LISTING', count($item));
$page->SetLoop ('CATEGORY',$category);
$page->SetParameter ('CAT_DROPDOWN',$cat_dropdown);
$page->SetLoop ('CAT',$cat);
/*Advertisement Fetching*/
$advertise_top = get_advertise("top");
$advertise_bottom = get_advertise("bottom");
$advertise_left = get_advertise("left_sidebar");
$advertise_right = get_advertise("right_sidebar");

$page->SetParameter('TOP_ADSCODE', $advertise_top['tpl']);
$page->SetParameter('TOP_ADSTATUS', $advertise_top['status']);
$page->SetParameter('BOTTOM_ADSCODE', $advertise_bottom['tpl']);
$page->SetParameter('BOTTOM_ADSTATUS', $advertise_bottom['status']);
$page->SetParameter('LEFT_ADSCODE', $advertise_left['tpl']);
$page->SetParameter('LEFT_ADSTATUS', $advertise_left['status']);
$page->SetParameter('RIGHT_ADSCODE', $advertise_right['tpl']);
$page->SetParameter('RIGHT_ADSTATUS', $advertise_right['status']);

if($advertise_left['status'] == 1 && $advertise_right['status'] == 1){
    $category_column = "col-md-8";
}else if($advertise_left['status'] == 0 && $advertise_right['status'] == 1){
    $category_column = "col-md-10";
}else if($advertise_left['status'] == 1 && $advertise_right['status'] == 0){
    $category_column = "col-md-10";
}else{
    $category_column = "col-md-12";
}

$page->SetParameter('CATEGORY_COLUMN', $category_column);
/*Advertisement Fetching*/
$page->SetParameter('BANNER_IMAGE', $config['home_banner']);
$page->SetParameter('LATITUDE', $mapLat);
$page->SetParameter('LONGITUDE', $mapLong);
$page->SetParameter('MAP_COLOR', $config['map_color']);
$page->SetParameter('ZOOM', $config['home_map_zoom']);
$page->SetParameter('DEFAULT_COUNTRY', get_countryName_by_sortname($sortname));
$page->SetParameter('SPECIFIC_COUNTRY', $sortname);

// Get Cron Job Settings
$cron_time = isset($config['cron_time']) ? $config['cron_time'] : time();
$cron_exec_time = isset($config['cron_exec_time']) ? $config['cron_exec_time'] : "86400";
if((time()-$cron_exec_time) > $cron_time) {
    run_cron_job();
}
$page->SetLoop('RECENT_BLOG', $recent_blog);
$page->SetLoop('TESTIMONIALS', $testimonials);
$page->SetParameter ('OVERALL_FOOTER', create_footer());
$page->CreatePageEcho();
//echo "Execution time : ".$time_elapsed_secs = microtime(true) - $start." Seconds";
?>