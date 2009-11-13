<?php
/*
Plugin Name: Sidebar Photoblog
Plugin URI: http://wpwave.com/plugins/sidebar-photoblog/
Description: Share your daily photos on your blog sidebar easily. 
Author: Hassan Jahangiry
Version: 2.0
Author URI: http://wpwave.com/
*/

//Search for $exclude_from_home=true; and change it false if you want to show photo posts in home page. 

if ( !defined('WP_PLUGIN_DIR') )  
			load_plugin_textdomain('sbp','wp-content/plugins/sidebar-photoblog');
 	else 
			load_plugin_textdomain('sbp', false, dirname(plugin_basename(__FILE__)));
			
			
function sphoto_install() {
global $wpdb;
$posttable=$wpdb->prefix . "posts";
	
		$post_date=date('Y-m-d H:i:s');
		$post_date_gmt=gmdate('Y-m-d H:i:s');
		$sql ="INSERT INTO $posttable
		(post_author, post_date, post_date_gmt, post_content, post_content_filtered, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_parent, menu_order, post_type)
		VALUES
		('1', '$post_date', '$post_date_gmt', '[sphoto_archive]', '', '".__('Photos','sbp')."', '', 'publish', 'closed', 'closed', '', 'photos', '', '', '$post_date', '$post_date_gmt', '0', '0', 'page');";

		$wpdb->query($sql);
		
		if (get_option('permalink_structure')) {
		$morelink=get_option('home').'/photos/';
		}else{
		$morelink=get_option('home').'/?page_id='.mysql_insert_id();
		}
		
		require_once(ABSPATH.'wp-admin/includes/taxonomy.php');
		
		$cat_id = wp_create_category(__('Sidebar Photoblog','sbp'),0);
		
		$newoptions = array(
				'title'=>__('Photoblog','sbp'), 
				'category'=>$cat_id, 
				'numphoto'=>'3',
				'border'=>'1',
				'opacity'=>'1',
				'morelink'=>$morelink,
				'hoverimage'=>'1',
				'rand'=>'0',
				'slide'=>'0'
				);
		update_option('thumbnail_size_w',100);
		update_option('thumbnail_size_h',100);
		update_option('widget_sphoto',$newoptions);

}//end function

if (!get_option('widget_sphoto')) sphoto_install();


function sphoto_get_post_attachments_id($post_id) {
global $wpdb; //only for images
	$ids = $wpdb->get_results($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_parent = %s AND (post_mime_type=\"image/jpeg\" OR post_mime_type=\"image/gif\" OR post_mime_type=\"image/png\") ORDER BY menu_order", $post_id));
	
	//if we can't find it!
	if (!$ids) {
		$ids = $wpdb->get_results($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE ID= %s +1 OR  ID= %s -1 AND (post_mime_type=\"image/jpeg\" OR post_mime_type=\"image/gif\" OR post_mime_type=\"image/png\") ORDER BY menu_order", $post_id,$post_id));;
	}
	
	if  ($ids)  {
		foreach ($ids as $id) {
			$result[]=$id->ID;	
		}
	}
	return $result;
}

function sphoto_get_post_img_url($post_id,$size='thumbnail',$sizeinfo=false) {
	//global $post;

	$meta=get_post_meta($post_id,'image-'.$size,false);

	if ($meta) {
		return $meta[0];
	}else{
		$attached_images=sphoto_get_post_attachments_id($post_id);

		$result=wp_get_attachment_image_src($attached_images[0],$size); //$attached_images[0] means first attached or image that has 					lowest menu order;//$result[0]: photo $result[1]:width ,$result[2]:height
		if ($sizeinfo)
			return $result;
		else
			return $result[0];
	}

}

function sphoto_archive_page() {
	$options=get_option('widget_sphoto');
	$size_w=get_option('thumbnail_size_w');
	$size_h=get_option('thumbnail_size_h');
	?>
	<div class="archive_sphoto">	
	<?php print_sphoto($options['category'],'-1',$size_w,$size_h,'0') ; ?>
	</div>
    
    <?php 
	
	if (!is_user_logged_in()) { ?>
    	<div style="font-size:11px;"><?php _e('By','sbp'); ?> <?php _e('<a href="http://wpwave.com/plugins/" title="Sidebar Photoblog WordPress Plugin">WordPress Sidebar Photoblog</a>','sbp'); ?></div>
	<?php }
	
}
add_shortcode('sphoto_archive', 'sphoto_archive_page');

function widget_sphoto_init() {
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return;	
	function widget_sphoto_control() {
		$options = $newoptions = get_option('widget_sphoto');
		if ( !is_array($newoptions) ) { //NOT happened never!!
			$newoptions = array(
				'title'=>__('Photoblog','sbp'), 
				'category'=>'', 
				'numphoto'=>'3',
				'border'=>'1',
				'opacity'=>'1',
				'morelink'=>'',
				'hoverimage'=>'1',
				'rand'=>'0',
				'slide'=>0
				);
			update_option('widget_sphoto',$newoptions); 
		}
		
		$options = get_option('widget_sphoto');
		$title = $options['title'];
		$category = $options['category'];
		$numphoto = $options['numphoto'];
		$border = $options['border'];
		$opacity = $options['opacity'];
		$morelink = $options['morelink'];
		$hoverimage = $options['hoverimage'];
		$rand = $options['rand'];
		$slide = $options['slide'];
		
		$size_w=get_option('thumbnail_size_w');
		$size_h=get_option('thumbnail_size_h');
	
			if ( $_POST['sphoto-submit'] ) {
				$newoptions['title'] = strip_tags(stripslashes($_POST['sphoto-title']));
				$newoptions['category'] = strip_tags(stripslashes($_POST['category']));
				$newoptions['numphoto'] = strip_tags(stripslashes($_POST['numphoto']));
				$newoptions['border'] = strip_tags(stripslashes($_POST['border']));
				$newoptions['opacity'] = strip_tags(stripslashes($_POST['opacity']));
				$newoptions['morelink'] = strip_tags(stripslashes($_POST['morelink']));
				$newoptions['hoverimage'] = strip_tags(stripslashes($_POST['hoverimage']));
				$newoptions['rand'] = strip_tags(stripslashes($_POST['rand']));
				$newoptions['slide'] = strip_tags(stripslashes($_POST['slide']));
				
				$size_w=strip_tags(stripslashes($_POST['size_w']));
				$size_h=strip_tags(stripslashes($_POST['size_h']));
				
				if (($size_w) && ($size_h)) {
				update_option('thumbnail_size_w',$size_w);
				update_option('thumbnail_size_h',$size_h);
				}
				if ( $options != $newoptions ) {
				$options = $newoptions;
				update_option('widget_sphoto', $options);
				}
			}
			
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$category = htmlspecialchars($options['category'], ENT_QUOTES);
		$numphoto = htmlspecialchars($options['numphoto'], ENT_QUOTES);
		$border = htmlspecialchars($options['border'], ENT_QUOTES);
		$opacity = htmlspecialchars($options['opacity'], ENT_QUOTES);
		$morelink = htmlspecialchars($options['morelink'], ENT_QUOTES);
		$hoverimage = htmlspecialchars($options['hoverimage'], ENT_QUOTES);
		$rand = htmlspecialchars($options['rand'], ENT_QUOTES);
		
		$size_w=get_option('thumbnail_size_w');
		$size_h=get_option('thumbnail_size_h');
		
		?>
        <p ><label for="sphoto-title"><?php _e('Title','sbp'); ?><br /> <input class="widefat" id="sphoto-title" name="sphoto-title" type="text" value="<?php echo $title; ?>" style="width:200px;"/></label></p>
        		
		<p>
        <label><?php _e('Photoblog category','sbp'); ?><br />
         <?php
		if (!$category)  echo '<strong>'.__('Currently: Nothing!</strong><br> Select a category as photoblog!','sbp').'<br />';?> 
        <?php wp_dropdown_categories('orderby=id&order=ASC&hide_empty=0&echo=1&selected='.$category.'&name=category');?></label>

        </p>
        
        <p>
        
        <label><?php _e('Number of photos','sbp'); ?>:
        <input type="text" name="numphoto" id="wnumphoto" value="<?php  echo $numphoto; ?>" style="width:40px;" /></label></p>
        
       
        <p>
        <?php _e('Size of photos','sbp'); ?><br/>
        <label><?php _e('Width','sbp'); ?> &nbsp;<input type="text" name="size_w" id="wsize" value="<?php echo $size_w;?>" style="width:40px;" /> px</label><br />
        <label><?php _e('Height','sbp'); ?> <input type="text" name="size_h" id="hsize" value="<?php echo $size_h;?>" style="width:40px;" /> px</label><br /></p>
        
   <label><?php _e('More link (Generated. No require to change)','sbp'); ?>  <br />
    <input type="text" name="morelink" id="morelink" value="<?php echo $morelink; ?>" style="width:250px;" /><br />
</label><small><?php _e('To hide more link leave it blank','sbp'); ?></small>
    
        
   <p>     
</p>

<input type="checkbox" name="slide" id="slide" value="1" <?php if ($slide) echo 'checked'; ?> />
<label for="border"><?php _e('Display photos as slideshow','sbp'); ?>
</label><br />

<input type="checkbox" name="rand" id="border" value="1" <?php if ($rand) echo 'checked'; ?> />
<label for="rand"><?php _e('Display random photos','sbp'); ?>
</label><br />

<input type="checkbox" name="opacity" id="opacity" value="1" <?php if ($opacity) echo 'checked'; ?> />
<label for="opacity"><?php _e('Enable opacity effect','sbp'); ?>
</label><br />	

<input type="checkbox" name="border" id="border" value="1" <?php if ($border) echo 'checked'; ?> />
<label for="border"><?php _e('Display photos with border and padding (Recommended)','sbp'); ?>
</label><br />





<input type="checkbox" name="hoverimage" id="hoverimage" value="1" <?php if ($hoverimage) echo 'checked'; ?> />
<label for="hoverimage"><?php _e('Display preview pop-up image effect','sbp'); ?>
</label><br/><br/>
<center>Do you like Sidebar Photoblog? <a href="http://www.wordpress.org/extend/plugins/sidebar-photoblog/">Vote it Up!</a></center><br/>

<input type="hidden" id="sphoto-submit" name="sphoto-submit" value="1" />
        <?php
	}


function widget_sphoto($args) {
	extract($args);
	$options = get_option('widget_sphoto');
	
	$title = $options['title'];
	$category = $options['category'];
	$numphoto = $options['numphoto'];
	$border = $options['border'];
	$opacity = $options['opacity'];
	$morelink = $options['morelink'];
	$hoverimage = $options['hoverimage'];
	$rand = $options['rand'];
	$slide = $options['slide'];
		
	$size_w=get_option('thumbnail_size_w');
	$size_h=get_option('thumbnail_size_h');
	
	
	 print "\n<!-- Start Sidebar Photoblog *** http://wpwave.com/plugins/ -->\n";

	echo $before_widget . $before_title . $title . $after_title;
	?>
	<br/><div class="widget_sphoto_body">

<?php  print_sphoto($category,$numphoto ,$size_w,$size_h,$hoverimage,$rand,$slide) ;

		if ($options['morelink']) echo '<br/><a href="'.$options['morelink'].'" title="'.__('More Photos','sbp').'">'.__('More Photos','sbp').'</a>';
		echo '</div>'.$after_widget;
		
	echo "\n<!-- End of Sidebar Photoblog Code -->\n";
	}
	
register_sidebar_widget('Sidebar Photoblog', 'widget_sphoto');
register_widget_control('Sidebar Photoblog', 'widget_sphoto_control', 345, 620);
}

//Don't worry if your theme isn't widgetized simply use this function : print_sphoto 
//Note: For Hoverimage make sure you enabled preview pop-up in the wdiget options because it needs some header code!
function print_sphoto($category,$numphoto=3,$size_w=100,$size_h=100,$hoverimage=false,$rand=0, $slide=0) {

	  if ($rand)
	  	$photos = new WP_Query('showposts='.$numphoto.'&cat='.$category.'&orderby=rand'); 
	  else
	 	$photos = new WP_Query('showposts='.$numphoto.'&cat='.$category); 
	  
	  $num=0;
	  if ($photos->have_posts()) : 
	  	while ($photos->have_posts() && (($num<$numphoto) || ($numphoto=='-1'))) : $photos->the_post(); 
	  
			$post_id=get_the_ID();
			$imgsrc=sphoto_get_post_img_url($post_id,$size='thumbnail',$sizeinfo=false);
				
			if ($hoverimage){ 
				$previewimage=sphoto_get_post_img_url($post_id,$size='medium',$sizeinfo=false);
			}
			// Testing $post_permalink=get_attachment_link($post_id);
	
			if ($imgsrc){ 
				$num++;
				$slide_images[]= $imgsrc;
                $slide_titles[]= get_the_title();
                $slide_previews[]= $previewimage;
                $slide_links[]= get_permalink();
                
                if (!$slide) { ?>
                    <span><a href="<?php echo the_permalink(); ?>" rel="bookmark" <?php if (!$hoverimage) { echo 'title="';the_title_attribute();echo '"';} ?> ><img src="<?php echo $imgsrc; ?>" <?php if (!$hoverimage) { echo 'alt="';the_title_attribute();echo '"';} ?> width="<?php echo $size_w; ?>px" height="<?php echo $size_h;?>px" <?php if ($hoverimage) { ?> onmouseover="preview('<div class=\'preview_caption\'><img src=\'<?php echo $previewimage;?>\' width=\'<?php echo get_option('medium_size_w'); ?>px\'><br/><center><?php the_title_attribute(); ?></center>');" onmouseout="hidepreview();"  <?php } ?>/></a></span>
               <?php
			   }
			 }
			   	 endwhile;

			else : ?>
			 <p class="center"><strong><?php _e('No photo currently!','sbp');?></strong></p>
			 <p> <?php _e('How can you add your photos here? See <a href="http://wpwave.com/plugins/sidebar-photoblog/">WordPress Sidebar Photoblog </a> or Readme.txt in the plugin directory','sbp');?></p>
		
			<?php 
			endif;
			
			if ($slide){ //slide  ?>
							<SCRIPT LANGUAGE="JavaScript">
                            <!-- Original:  Mike Canonigo (mike@canonigo.com) -->
                            <!-- Web Site:  http://www.munkeehead.com -->
							<!-- Begin
							var NewImg = Array();
							var NewTitle =Array();
							var NewPreview =Array();
							var NewLink =Array();
                            <?php for ($j=0;$j<=$numphoto-1;$j++) {  
                                 	  echo "NewImg[$j] = '$slide_images[$j]';"; 
                                      echo "NewTitle[$j] = '$slide_titles[$j]'; ";
                                      echo "NewPreview[$j] = '$slide_previews[$j]'; ";
                                      echo "NewLink[$j] = '$slide_links[$j]'; ";
                         		  } ?>
							   
                            var ImgNum = 0;
                            var ImgLength = NewImg.length - 1;
                            
                            var delay = 5000;
                            
                            var lock = false;
                            var run;
							auto();
                            function chgImg(direction) {
                                if (document.images) {
                                    ImgNum = ImgNum + direction;
                                    if (ImgNum > ImgLength) {
                                        ImgNum = 0;
                                    }
                                    if (ImgNum < 0) {
                                        ImgNum = ImgLength;
                                    }
                                    document.slideshow.src = NewImg[ImgNum];
                                    
                                    document.getElementById("slide_link").href = NewLink[ImgNum];
									document.getElementById("slide_link").title = NewTitle[ImgNum];
                                   
									
                                }
                            }
                            function auto() {
                            if (lock == true) {
                            lock = false;
                            window.clearInterval(run);
                            }
                            else if (lock == false) {
                            lock = true;
                            run = setInterval("chgImg(1)", delay);
                               }
                            }
                            //  End -->
                            </script>

                        <span><a href="<?php echo $slide_links[0]; ?>" id="slide_link"  rel="bookmark" <?php if (!$hoverimage) { echo 'title="'.$slide_titles[0];echo '"';} ?> ><img src="<?php echo $slide_images[0]; ?>" name="slideshow" id="slideshowid" alt="<?php echo $slide_titles[0]; ?>" width="<?php echo $size_w; ?>px" height="<?php echo $size_h;?>px" /> </a></span>
                        
                    <center><table style="width:120px;">
                    <tr>
                   
                    <td align="right"><a href="javascript:chgImg(-1)" title="<?php _e('Prev','sbp'); ?>"><img src="<?php echo WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)); ?>/next.png" border="0" /></a></td>
                    <td align="center"><a href="javascript:auto()" title="<?php _e('Auto/Stop','sbp'); ?>" ><img src="<?php echo WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)); ?>/play.png" border="0" /></a></td>
                    <td align="left"><a href="javascript:chgImg(1)" title="<?php _e('Next','sbp'); ?>" ><img src="<?php echo WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)); ?>/prev.png" border="0" /></a></td>
                    </tr>
                    </table></center>
                    
			  <?php } ?>          
     

   
	
	<?php
	
	
}


function sphoto_header(){
$options=get_option('widget_sphoto');
$negcat="-".$options['category'];

$exclude_from_home=true; //Change it false if you want to show photo posts in home page. 

if ($exclude_from_home) {
	global $wp_query;
	if ( (!is_single()) && (!is_page()) ) 
	query_posts(
		array_merge(
			array('cat' => $negcat),
			$wp_query->query
		)
	);
}




echo "\n<!-- Sidebar Photoblog Widget Style http://wpwave.com/plugins/ -->\n";	
?>
		<style type="text/css" media="screen">
		.archive_sphoto{text-align:left;}
		.archive_sphoto img{border:1px solid #CCC;-moz-border-radius:3px;-khtml-border-radius:3px;-webkit-border-radius:3px;border-radius:3px;padding:3px;margin:0px 5px 5px 0px;}
		.archive_sphoto img:hover{border:1px solid #444;padding:3px;}
		<?php  
		if ($options['hoverimage']) { ?>
		.preview{ font-family: Tahoma, Arial, Verdana, Helvetica, sans-serif; font-size: 11px; font-weight: bold; color: #55523E; padding-left:10px; padding-top:10px; padding-bottom:10px;}
		.preview_caption{padding:3px;background-color:#dddddd;}
		.preview_caption img {}
		<?php
	    }
		if ($options['border']) {
		?>
		.widget_sphoto_body{text-align:center}
		.widget_sphoto_body img{border:1px solid #CCC;-moz-border-radius:3px;-khtml-border-radius:3px;-webkit-border-radius:3px;border-radius:3px;padding:3px;margin:0px 5px 5px 0px;}
		.widget_sphoto_body img:hover{border:1px solid #6699FF;padding:3px;}
		<?php 
		}
		if  ($options['opacity']) { ?>
		.widget_sphoto_body img,.archive_sphoto img{filter:alpha(opacity=70);opacity:0.7;-moz-opacity:0.7;}
		.widget_sphoto_body img:hover,.archive_sphoto img:hover{filter:alpha(opacity=100);opacity:1;-moz-opacity:1;}
		<?php
		} ?>
</style>
		<?php 
		if  ($options['hoverimage']) { 
		$med_w=get_option('medium_size_w');?>
        <table style="position:absolute;top:-500px;left:0px;z-index:500;font-family:Tahoma,sans-serif;font-size:11px;-moz-opacity:1;width:<?php echo $med_w; ?>px;border:none;" id="preview" cellspacing=0 cellpadding=0>
        <tr><td style="color:#000000;" id="cellpreview">
        </td></tr></table>
        <input type="hidden" id="usepreview" value = "0" />
                <script>
            var isIE = document.all?true:false;
            if (!isIE) document.captureEvents(Event.MOUSEMOVE);
            document.onmousemove = getMousePosition;
            function getMousePosition(e) {
            if(document.getElementById("usepreview").value == 1){
              var _x;
              var _y;
              if (!isIE) {
                _x = e.pageX;
                _y = e.pageY;
              }
              if (isIE) {
                _x = event.clientX - -document.documentElement.scrollLeft;
                _y = event.clientY - -document.documentElement.scrollTop;
              }
              xnum = _x - -10;
              ynum = _y - -15;
        
              document.getElementById("preview").style.top = ynum + "px"
        
              if((xnum - (-1 * document.getElementById("preview").scrollWidth) < (screen.width - 40))){
              document.getElementById("preview").style.left = xnum + "px"
              }else{
              document.getElementById("preview").style.left = (screen.width - document.getElementById("preview").scrollWidth - 40) + "px";
              }
        
              document.getElementById("preview").style.top = ynum + "px"
        
              return true;
              }
            }
        
            function preview(msg){
            document.getElementById("cellpreview").innerHTML = msg;
            document.getElementById("usepreview").value = 1;
            }
        
            function hidepreview(){
              document.getElementById("preview").style.left = "-500px";
              document.getElementById("preview").style.top = "-500px";
            document.getElementById("usepreview").value = 0;
            }
        </script>

<?php } ?>

<?php
}

add_action('wp_head','sphoto_header');
add_action('plugins_loaded', 'widget_sphoto_init');

function sphoto_list_categories(&$args) {
//Thanks to Advanced Category Excluder

  	$opt=get_option('widget_sphoto');
    $cats = array($opt['category']) ;
  
    if (count($cats) > 0)
    /* if there is any category to hide :) */
    {
      $args = str_replace('</h2><ul>','</h2><ul>'.chr(10),$args);
      /* Insert a line break after the heading. */
      $rows = explode("\n",$args);    
      $p = "";
      for ($i=0; $i <= count($cats); $i++)
      /* 
        Yes, we are now creating a regular expression for ereg.
      */
      {
        if ($cats[$i] != "")
        {
          $catData = get_category($cats[$i]);
          /**
           * Here we get the name of the category, because that's the only thing we can exclude by
           *  in early 2.x versions of WordPress           
           */
          $p .= $catData->cat_name;
          if ($i+1 < count($cats)) $p .= "|";
          /**
           * If we'll get more object to exclude we add a PREG pattern OR, which is a pipe '|'
           */                     
        }
        $pattern = "(".$p.")";
      }
      if (!empty($pattern))
      {
        for($j = 0; $j <= count($rows); $j++ )
        {
          if(preg_match("/\b".$pattern."\b/i",$rows[$j]))
          /* We have the <li> starting tag on the first line, and the ending on de second line, so we kill'em all :)*/ 
          {
              unset($rows[$j]);
              unset($rows[$j+1]);
          }
        }
        $args = implode("\n",$rows);
      }
    }
  return($args);
}

add_filter('wp_list_categories','sphoto_list_categories');	
?>