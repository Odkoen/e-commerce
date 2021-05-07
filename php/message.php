<?php
if(checkloggedin()) {

    if(isset($_GET['postid'])){
        $postid = base64_url_decode($_GET['postid']);
    }else{
        $postid = '';
    }
    $userid = '';
    $chatid = '';
    $chat_username = '';
    $chat_fullname = '';
    $chat_userimg = '';
    $chat_userstatus = '';

    if(isset($_GET['userid'])){
        $userid = base64_url_decode($_GET['userid']);
        $userdata = get_user_data(null,$userid);
        if($userdata){
            $chatid = $userid.'_'.$postid;
            $chat_username = $userdata['username'];
            $chat_fullname = ($userdata['name'] != '')? $userdata['name'] : $userdata['username'];
            $chat_userimg = ($userdata['image'] == "")? "default_user.png" : $userdata['image'];
            $chat_userstatus  = ($userdata['online'] == "0")? "offline" : "online";
        }
    }

    $ses_userdata = get_user_data($_SESSION['user']['username']);
    $author_image = $ses_userdata['image'];
    if($config['quickchat_ajax_on_off'] == 'on' || $config['quickchat_socket_on_off'] == 'on') {
        $page = new HtmlTemplate ("templates/" . $config['tpl_name'] . "/quickchat.tpl");
        $page->SetParameter ('OVERALL_HEADER', create_header($lang['MESSAGE']));
        $page->SetParameter('PAGE_TITLE', $lang['MESSAGE']);
        $page->SetParameter('PAGE_META_KEYWORDS', $config['meta_keywords']);
        $page->SetParameter('LANGUAGE_DIRECTION', get_current_lang_direction());
        $page->SetParameter('PAGE_META_DESCRIPTION', $config['meta_description']);
        $page->SetParameter('USERIMG', $author_image);
        $page->SetParameter('CHATID',$chatid);
        $page->SetParameter('POSTID',$postid);
        $page->SetParameter('CHAT_USERID',$userid);
        $page->SetParameter('CHAT_FULLNAME',$chat_fullname);
        $page->SetParameter('CHAT_USERIMG',$chat_userimg);
        $page->SetParameter('CHAT_USERSTATUS',$chat_userstatus);
        $page->SetParameter('COPYRIGHT_TEXT', $config['copyright_text']);
        $page->CreatePageEcho();
    }
    elseif($config['wchat_on_off'] == 'on') {

        $page = new HtmlTemplate ("templates/" . $config['tpl_name'] . "/wchat.tpl");
        $page->SetParameter('OVERALL_HEADER', create_header($lang['MESSAGE']));
        $page->SetParameter('PAGE_TITLE', $lang['MESSAGE']);
        $page->SetParameter('PAGE_META_KEYWORDS', $config['meta_keywords']);
        $page->SetParameter('LANGUAGE_DIRECTION', get_current_lang_direction());
        $page->SetParameter('PAGE_META_DESCRIPTION', $config['meta_description']);
        $page->SetParameter('USERIMG', $author_image);
        $page->SetParameter('CHATID',$chatid);
        $page->SetParameter('POSTID',$postid);
        $page->SetParameter('CHAT_USERNAME',$chat_username);
        $page->SetParameter('CHAT_USERID',$userid);
        $page->SetParameter('CHAT_FULLNAME',$chat_fullname);
        $page->SetParameter('CHAT_USERIMG',$chat_userimg);
        $page->SetParameter('CHAT_USERSTATUS',$chat_userstatus);
        $page->SetParameter('COPYRIGHT_TEXT', $config['copyright_text']);
        $page->CreatePageEcho();
    }else
        error($lang['PAGE_NOT_FOUND'], __LINE__, __FILE__, 1);
}else
    headerRedirect($link['LOGIN']);
?>