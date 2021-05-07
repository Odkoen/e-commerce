<?php
require_once('includes.php');

if(isset($_POST['tpl_name']))
{
    if(!check_allow()){
        ?>
        <script src="plugins/bower_components/jquery/dist/jquery.min.js"></script>
        <script>
            $(document).ready(function(){
                $('#sa-title').trigger('click');
            });
        </script>
        <?php

    }
    else {
        update_option("tpl_name",$_POST['tpl_name']);

        transfer('themes.php', 'Theme Changed');
        exit;
    }
}

function folder_exist($folder)
{
    // Get canonicalized absolute pathname
    $path = realpath($folder);

    // If it exist, check if it's a directory
    if($path !== false AND is_dir($path))
    {
        // Return canonicalized absolute pathname
        return $path;
    }

    // Path/folder does not exist
    return false;
}

?>
<style>
    .pro-list-img{
        min-height: 264px;
        background-image: url("assets/images/quickad_loading.gif");
        background-repeat: no-repeat;
        background-position: center;
    }
</style>
<script language="JavaScript">
    <?php
    echo "\n";
    echo '  var img=new Array();';
    echo "\n";
    if ($handle = opendir('../templates/'))
    {
        while (false !== ($file = readdir($handle)))
        {
            if ($file != "." && $file != "..")
            {
                echo 'img["' . $file . '"]="../templates/' . $file . '/screenshot.png";';
                echo "\n";
            }
        }
        closedir($handle);
    }
    ?>

    function swap(type){
        document.getElementById("imgMain").src=img[type];
        var sel=document.shoeFrm.shoeSel;
        for(i=0;i<sel.length;i++){if(sel.options[i].text==type)
        {
            sel.selectedIndex=i;}}
    }
</script>

<main class="app-layout-content">

    <!-- Page Content -->
    <div class="container-fluid p-y-md">
        <!-- Partial Table -->
        <div class="card">
            <div class="card-header">
                <h4>Themes ( <?php echo $config['tpl_name'] ?> )</h4>
                <div class="pull-right">
                    <a href="setting.php#quickad_theme_setting" class="btn btn-success waves-effect waves-light m-r-10">Theme setting</a>
                </div>
            </div>
            <div class="card-block">
                <!-- /row -->
                <div class="row">

                    <?php
                    $tpl_name = $config['tpl_name'];
                    if ($tpl_name != "." && $tpl_name != "..")
                    {
                        $filepath = "../templates/" . $tpl_name . "/theme-info.txt";
                        if(file_exists($filepath)){
                            $themefile = fopen($filepath,"r");

                            $themeinfo = array();
                            while(! feof($themefile)) {
                                $lineRead = fgets($themefile);
                                if (strpos($lineRead, ':') !== false) {
                                    $line = explode(':',$lineRead);
                                    $key = trim($line[0]);
                                    $value = trim($line[1]);
                                    $themeinfo[$key] = $value;
                                }
                            }
                            ?>
                            <div class="col-sm-6 col-md-4 col-lg-4 pad-10">
                                <div class="white-box pro-box p-0">
                                    <div class="pro-list-img">
                                        <img src="../templates/<?php echo $tpl_name ?>/screenshot.png" width="100%"/>
                                    </div>
                                    <div class="pro-content-3-col">
                                        <div class="pro-list-details">
                                            <h4>
                                                <a class="text-dark" href="#"><?php echo $themeinfo['Theme Name'] ?></a>
                                            </h4>
                                            <h4 class="text-danger"><small>Author</small> <?php echo $themeinfo['Author'] ?></h4> Price: <?php echo $themeinfo['Price'] ?>
                                        </div>
                                    </div>

                                    <hr class="m-0">
                                    <div class="pro-agent-col-3">
                                        <div class="agent-name">
                                            <button class="btn btn-default btn-rounded waves-effect waves-light btn-sm" type="button"><span class="btn-label"><i class="ti-check"></i></span>Current Theme</button>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <?php
                            fclose($themefile);
                        }
                    }
                    ?>

                    <?php
                    if ($handle = opendir('../templates/'))
                    {
                        while (false !== ($folder = readdir($handle)))
                        {
                            if ($folder != "." && $folder != ".." && $folder!= $config['tpl_name'])
                            {
                                $filepath = "../templates/" . $folder . "/theme-info.txt";
                                if(file_exists($filepath)){
                                    $themefile = fopen($filepath,"r");

                                    $themeinfo = array();
                                    while(! feof($themefile)) {
                                        $lineRead = fgets($themefile);
                                        if (strpos($lineRead, ':') !== false) {
                                            $line = explode(':',$lineRead);
                                            $key = trim($line[0]);
                                            $value = trim($line[1]);
                                            $themeinfo[$key] = $value;
                                        }
                                    }
                                    ?>
                                    <div class="col-sm-6 col-md-4 col-lg-4 pad-10">
                                        <div class="white-box pro-box p-0">
                                            <div class="pro-list-img">
                                                <img src="../templates/<?php echo $folder ?>/screenshot.png" width="100%"/>
                                            </div>
                                            <div class="pro-content-3-col">
                                                <div class="pro-list-details">
                                                    <h4>
                                                        <a class="text-dark" href="#"><?php echo $themeinfo['Theme Name'] ?></a>
                                                    </h4>
                                                    <h4 class="text-danger"><small>Author</small> <?php echo $themeinfo['Author'] ?></h4> Price: <?php echo $themeinfo['Price'] ?>
                                                </div>
                                            </div>

                                            <hr class="m-0">
                                            <div class="pro-agent-col-3">
                                                <div class="agent-name">
                                                    <form action="themes.php" method="post" name="f1" id="f1">
                                                        <input type="hidden" value="<?php echo $folder ?>" name="tpl_name">
                                                        <?php
                                                        if($folder == $config['tpl_name'])
                                                        {
                                                            echo '<button class="btn btn-default btn-rounded waves-effect waves-light btn-sm" type="button"><span class="btn-label"><i class="ti-check"></i></span>Current Theme</button>';
                                                        }
                                                        else{
                                                            echo '<button class="btn btn-success btn-rounded waves-effect waves-light btn-sm" type="submit"><span class="btn-label"><i class="ti-check"></i></span>Activate Me</button>';
                                                        }
                                                        ?>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                    <?php
                                    fclose($themefile);
                                }
                            }
                        }
                        closedir($handle);
                    }

                    ?>

                    <?php
                    $folder = "../templates/modern-theme/";
                    if (!folder_exist($folder))
                    {
                        ?>
                        <div class="col-sm-6 col-md-4 col-lg-4 pad-10">
                            <div class="white-box pro-box p-0">
                                <div class="pro-list-img">
                                    <img src="assets/images/mydiary-theme-screenshot.png" width="100%">
                                </div>
                                <div class="pro-content-3-col">
                                    <div class="pro-list-details">
                                        <h4>
                                            <a class="text-dark" href="#">Modern Bootstrap 4 Template</a>
                                        </h4>
                                        <h4 class="text-danger"><small>Author</small> Bylancer</h4> Price: $29</div>
                                </div>

                                <hr class="m-0">
                                <div class="pro-agent-col-3">
                                    <div class="agent-name">
                                        <a href="https://codecanyon.net/item/modern-quickad-classified-template/25051810" target="_blank" class="btn btn-success btn-rounded waves-effect waves-light btn-sm">
                                            <span class="btn-label"><i class="ti-check"></i></span>Buy Now</a>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                    $folder = "../templates/classic-theme/";
                    if (!folder_exist($folder))
                    {
                        ?>
                        <div class="col-sm-6 col-md-4 col-lg-4 pad-10">
                            <div class="white-box pro-box p-0">
                                <div class="pro-list-img">
                                    <img src="https://codentheme.com/product-images/approved/classic-theme-banner-5ee34e0c257a50.72060916.png" width="100%">
                                </div>
                                <div class="pro-content-3-col">
                                    <div class="pro-list-details">
                                        <h4>
                                            <a class="text-dark" href="#">Material Theme</a>
                                        </h4>
                                        <h4 class="text-danger"><small>Author</small> Bylancer</h4> Free</div>
                                </div>

                                <hr class="m-0">
                                <div class="pro-agent-col-3">
                                    <div class="agent-name">
                                        <a href="https://codentheme.com/item/material-quickad-classified-ads-script-template/" class="btn btn-success btn-rounded waves-effect waves-light btn-sm" target="_blank">
                                            <span class="btn-label"><i class="ti-check"></i></span>Free Download</a>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                    $folder = "../templates/material-theme/";
                    if (!folder_exist($folder))
                    {
                        ?>
                        <div class="col-sm-6 col-md-4 col-lg-4 pad-10">
                            <div class="white-box pro-box p-0">
                                <div class="pro-list-img">
                                    <img src="https://codentheme.com/product-images/approved/material-theme-banner-5ee3518e211084.56099272.png" width="100%">
                                </div>
                                <div class="pro-content-3-col">
                                    <div class="pro-list-details">
                                        <h4>
                                            <a class="text-dark" href="#">Classic Theme</a>
                                        </h4>
                                        <h4 class="text-danger"><small>Author</small> Bylancer</h4> Price: Free</div>
                                </div>

                                <hr class="m-0">
                                <div class="pro-agent-col-3">
                                    <div class="agent-name">
                                        <a href="https://codentheme.com/item/classic-quickad-classified-ads-script-template/" class="btn btn-success btn-rounded waves-effect waves-light btn-sm">
                                            <span class="btn-label"><i class="ti-check"></i></span>Free Download</a>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                </div>


            </div>
            <!-- .card-block -->
        </div>
        <!-- .card -->
        <!-- End Partial Table -->

    </div>
    <!-- .container-fluid -->
    <!-- End Page Content -->

</main>


<?php include('footer.php'); ?>
</body>

</html>