<?php
    require_once('class/funcs.php');

    if(!isset($_GET['id'])){
        print('No ID !');
        exit();
    }

    if(!empty($_GET['id'])){
        $p_id = $_GET['id'];
    }

    /////////////////////////////////////// FLICKR API ///////////////////////////////////////
    $flickr_api_req=@file_get_contents(Flickr_get('flickr.people.getPublicPhotos',array('user_id' => $flickr_uid,'per_page'=>500)));
    $flickr_api_json=json_decode($flickr_api_req,true);
    
    if(!isset($flickr_api_req) or empty($flickr_api_req)){
        print('Error, Flickr API not responding');
        exit;
    }
    
    $flickr_nb_photos=$flickr_api_json['photos']['total'];
    ////////////////////////////////////////////////////////////////////////////////////////////////
    
    if(file_exists('instagram_data.json')){
        $instagram_photos_arr=json_decode(file_get_contents('instagram_data.json'),true);
    }else{
        $instagram_photos_arr=array();
    }
    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.1.1">

    <link rel="stylesheet" href="css/bootstrap.min.css" >
    <link rel="stylesheet" href="css/album.css" >
    <link rel="stylesheet" type="text/css" href="css/main.css">

    <link rel="stylesheet" href="css/image-zoom.css" />

    

    <link rel="stylesheet" href="css/animate.css">
    
    <script src="js/jquery-3.5.1.min.js" ></script>
    <script src="js/bootstrap.min.js" ></script>
    <script src="js/main.js" ></script>
    <script src="js/image-zoom.min.js"></script>
	
	<link rel="apple-touch-icon" sizes="57x57" href="res/favicon/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="res/favicon/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="res/favicon/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="res/favicon/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="res/favicon/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="res/favicon/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="res/favicon/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="res/favicon/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="res/favicon/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="res/favicon/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="res/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="res/favicon/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="res/favicon/favicon-16x16.png">
	<meta name="msapplication-TileImage" content="res/favicon/ms-icon-144x144.png">
	<link rel="shortcut icon" href="res/favicon/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="res/favicon/favicon.ico" type="image/x-icon">

    <title>GPicture - View (<?php print($_GET['id']); ?>)</title>
    
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
</head>

<body style="background-color: rgb(100, 100, 100);">
    <div class="blur"></div>
    <?php require_once('res/top.php'); ?>

    <main role="main">
                    <?php
                        foreach($flickr_api_json['photos']['photo'] as $i => $data){
                            if($p_id==$data['id']){
                                $desc="";
                                $inst_url="";
                                $inst_url_t="";
                                $cust_size="";
                                $unknown_str="?";
                                $p_title=$data['title'];
                                preg_match('/(.*)\.[^.]+$|(?!\s)(.*)/', $p_title, $p_title_wh_ext);
                                if(isset($p_title_wh_ext[2])){
                                    $p_title_wh_ext=$p_title_wh_ext[2];
                                }else{
                                    $p_title_wh_ext=$p_title_wh_ext[1];
                                }

                                if(isset($flickr_api_json['photos']['photo'][$i-1])){
                                    $prev_img_uid=$flickr_api_json['photos']['photo'][$i-1];
                                    $prev_btn='
                                        <div class="prev_btn">
											<a class="badge badge-primary" href="pic?id='.$prev_img_uid['id'].'">
												<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
													<path fill-rule="evenodd" d="M5.854 4.646a.5.5 0 0 1 0 .708L3.207 8l2.647 2.646a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 0 1 .708 0z"/>
													<path fill-rule="evenodd" d="M2.5 8a.5.5 0 0 1 .5-.5h10.5a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
												</svg>
											</a>
                                        </div>
                                    ';
                                }else{
                                    $prev_img_uid=-1;
                                    $prev_btn='';
                                }

                                if(isset($flickr_api_json['photos']['photo'][$i+1])){
                                    $next_img_uid=$flickr_api_json['photos']['photo'][$i+1];
                                    $next_btn='
                                        <div class="next_btn">
                                            <a class="badge badge-primary" href="pic?id='.$next_img_uid['id'].'">
												<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
													<path fill-rule="evenodd" d="M10.146 4.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L12.793 8l-2.647-2.646a.5.5 0 0 1 0-.708z"/>
													<path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5H13a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 8z"/>
												</svg>
                                            </a>
                                        </div>
                                    ';
                                }else{
                                    $next_img_uid=-1;
                                    $next_btn='';
                                }

                                $p_url=$data['url_o'];
                                $p_url_back=str_replace('_o.jpg','_b.jpg',$p_url);

                                $p_width=$data['width_o'];
                                $p_height=$data['height_o'];
                                $p_size=$p_width."x".$p_height;

                                $Pic_Inf=(GetPicInf($p_id));
                                $Pic_Exif=(GetExif($p_id));

                                if(!isset($Pic_Exif['exif']['Flash'])){$Pic_Exif['exif']['Flash']=$unknown_str;}
                                if(!isset($Pic_Exif['exif']['Software'])){$Pic_Exif['exif']['Software']=$unknown_str;}
                                if(!isset($Pic_Exif['exif']['Exposure Program'])){$Pic_Exif['exif']['Exposure Program']=$unknown_str;}
                                if(!isset($Pic_Exif['exif']['Focal Length'])){$Pic_Exif['exif']['Focal Length']=$unknown_str;}
                                if(!isset($Pic_Exif['exif']['Aperture'])){$Pic_Exif['exif']['Aperture']=$unknown_str;}
                                if(!isset($Pic_Exif['exif']['ISO Speed'])){$Pic_Exif['exif']['ISO Speed']=$unknown_str;}

                                $Hid = hash('sha1',strtotime(date("d/m/Y H:i:s",$Pic_Inf['date']['date_taken'])).$p_title_wh_ext);
                                if(isset($instagram_photos_arr[$Hid])){
                                    $flickr_instagram_link=($instagram_photos_arr[$Hid]);
                                }else{
                                    $flickr_instagram_link=0;
                                }

                                if($flickr_instagram_link!==0){
                                    $desc="Description\n".$flickr_instagram_link['desc'];
                                    $inst_url_t="Insta: ".$flickr_instagram_link['url']."\n";
                                    $inst_url=$flickr_instagram_link['url'];
                                }
                               
                                if($Pic_Exif['exif']['Flash']=="Off, Did not fire"){
                                    $Flash="⚡ Off";
                                }else{
                                    $Flash="⚡ On";
                                }

                                if($p_height>2000 & $p_width<3300){
                                    $cust_size="width:26%;";
                                }

                                if($p_height<2000){
                                    $size_val_back="350%"; // pano h
                                }else{
                                    $size_val_back="cover";
                                }

                                $Soft=str_replace("Adobe Photoshop","",str_replace("(Windows)","",$Pic_Exif['exif']['Software']));
                                
                                //set Background
                                print('
                                <style>
                                    .blur{
                                        background-image: url(\''.$p_url_back.'\') !important;
                                        background-size: '.$size_val_back.';
                                    }
                                </style>
                                ');
                                
                                if($inst_url!=''){
                                    $Insta_Btn='<a target="_blank" href="'.$inst_url.'"><img src="res/1024px-Instagram_icon.png"/></a>';
                                }else{
                                    $Insta_Btn='';
                                }

                                print('
                                    <div class="main_img_view">
                                        
                                        <center style="display:inline-block;width: 100%;">
                                            '.$prev_btn.'
                                            <div class="img_view" style="'.$cust_size.'">
                                                <!--<img src="'.$p_url.'" width=100% height=100% alt="...">-->
                                                <img id="imageZoom" src="'.$p_url.'" />
                                            </div>
                                            '.$next_btn.'
                                        </center>
                                        

                                        <div class="exif_view animate__animated animate__fadeInRight animate__delay-1s animate__faster">
                                            <div class="exif_infos">
                                                <h2>- EXIF INFO -</h2>
                                                <p>
                                                <hr>
                                                <table border=0 style="width:100%;text-align:center;">
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="2">
																<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-camera-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
																	<path d="M10.5 8.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
																	<path fill-rule="evenodd" d="M2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2zm.5 2a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zm9 2.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0z"/>
																</svg>
                                                                 '.$Pic_Exif['exif']['Model'].'
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                '.$Soft.'
                                                            </td>
                                                            <td>
                                                                '.$Pic_Exif['exif']['Exposure Program'].'
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                ⭕ ƒ/'.$Pic_Exif['exif']['Aperture'].'
                                                            </td>
                                                            <td>
                                                                👁 '.$Pic_Exif['exif']['Focal Length'].'
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                ⏱️ '.$Pic_Exif['exif']['Exposure'].'
                                                            </td>
                                                            <td>
                                                                ISO '.$Pic_Exif['exif']['ISO Speed'].'
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                '.$Flash.'
                                                            </td>
                                                            <td>
                                                                📅 '.date("d/m/Y - H:i",$Pic_Inf['date']['date_taken']).'
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        
                                        <div class="open_link animate__animated animate__slideInDown animate__delay-2s animate__faster">
                                            <h3>Open on </h3>
                                            '.$Insta_Btn.'
                                            <a target="_blank" href="https://flickr.com/photos/'.$flickr_username.'/'.$p_id.'"><img src="res/1200px-Flickr.svg.png"/></a>
                                        </div>

                                        <center>
                                            <div class="pic_info_view">
                                                <div>
                                                    <h2>'.$p_title_wh_ext.'</h2>
                                                    <a target="_blank" href="https://flickr.com/photos/'.$flickr_username.'/'.$p_id.'">Open Flickr link</a>
                                                    <p>
                                                    <a target="_blank" href="'.$inst_url.'">Open Instagram link</a>
                                                    <p>
                                                    <hr>
                                                    Size: '.$p_size.'
                                                    <br>
                                                    '.nl2br($desc).'
                                                    
                                                </div>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                            </div>
                                        </center>
                                        
                                    </div><br><br><br>
                                ');
                            }
                        }
                    ?>
                <!-- </div>
            </div>
        </div> -->

    </main>

    <script>
        $(function(){
			$('#imageZoom').imageZoom({zoom : 200});
	    });
    </script>

    <?php require_once('res/bottom.php'); ?>

    <?php
        $str_content="📷 ".$Pic_Exif["exif"]["Model"]." (".$Soft.")\n &nbsp; ⭕ ƒ/".$Pic_Exif["exif"]["Aperture"]." - 👁 ".$Pic_Exif["exif"]["Focal Length"]."\n &nbsp; ⏱️ ".$Pic_Exif["exif"]["Exposure"]." - ISO ".$Pic_Exif["exif"]["ISO Speed"]."\n &nbsp; 📅 ".date("d/m/Y - H:i",$Pic_Inf['date']['date_taken']);

        print('
            <meta property="og:site_name" content="GPic - Viewer" >
            <meta property="og:title" content="'.$p_title_wh_ext.'" />
            <meta property="og:image" content="'.$p_url.'" />
            <meta property="og:description" content="'.$str_content."\n-----------------------------------------------\n".$desc.'" />
            
            <meta name="theme-color" content="#2572E5" />

            <!-- Include this to make the og:image larger -->
            <meta name="twitter:card" content="summary_large_image">

        ');

    ?>

</body>
</html>