<?php
/*
Plugin Name: Sidebar Photoblog
Plugin URI: http://wordpresswave.com/plugins/
Description: Share your daily/family photos on your blog sidebar easily. 
Author: Hassan Jahangiry
Version: 1.36
Author URI: http://wordpresswave.com/
*/

$exclude_from_home=true; //Change it false if you want to show photo posts in home page. 

function sphoto_install() {
global $wpdb;
$posttable=$wpdb->prefix . "posts";
	
		$post_date=date('Y-m-d H:i:s');
		$post_date_gmt=gmdate('Y-m-d H:i:s');
		$sql ="INSERT INTO $posttable
		(post_author, post_date, post_date_gmt, post_content, post_content_filtered, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_parent, menu_order, post_type)
		VALUES
		('1', '$post_date', '$post_date_gmt', '[sphoto_archive]', '', 'Browse Photos', '', 'publish', 'closed', 'closed', '', 'browse', '', '', '$post_date', '$post_date_gmt', '0', '0', 'page');";

		$wpdb->query($sql);
		
		if (get_option('permalink_structure')) {
		$morelink=get_option('home').'/browse/';
		}else{
		$morelink=get_option('home').'/?p='.mysql_insert_id();
		}
		
		require_once(ABSPATH.'wp-admin/includes/taxonomy.php');
		
		$cat_id = wp_create_category('Sidebar Photoblog',0);
		
	
		$newoptions = array(
				'title'=>'Photoblog', 
				'category'=>$cat_id, 
				'numphoto'=>'3',
				'border'=>'1',
				'opacity'=>'1',
				'morelink'=>$morelink,
				'hoverimage'=>'1',
				);
		update_option('widget_sphoto',$newoptions);
		update_option('thumbnail_size_w',100);
		update_option('thumbnail_size_h',100);

}//end function

if (!get_option('widget_sphoto')) sphoto_install();


function sphoto_get_post_attachments_id($post_id) {
global $wpdb; //only for images
	$ids = $wpdb->get_results($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_parent = %s AND (post_mime_type=\"image/jpeg\" OR post_mime_type=\"image/gif\" OR post_mime_type=\"image/png\") ORDER BY menu_order", $post_id));
			if ( ($ids) ) {
				foreach ($ids as $id) {
					$result[]=$id->ID;	
				}
			}
			return $result;
}

function sphoto_get_post_img_url($post_id,$size='thumbnail',$sizeinfo=false) {
global $post;

	$meta=get_post_meta($post_id,'image-'.$size,false);

	if ($meta) {
	return $meta[0];
	}else{
	$attached_images=sphoto_get_post_attachments_id($post_id);

	$result=wp_get_attachment_image_src($attached_images[0],$size); //$attached_images[0] means first attached or image that has 					lowest menu order;
	//$result[0]: photo $result[1]:width ,$result[2]:height
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
	<?php print_sphoto($options['category'],'-1',$size_w,$size_h) ; ?>
	</div>
    
    <?php 
	
	if (!is_user_logged_in()) { ?>
    <small>By <a href="http://wordpresswave.com/plugins/" title="Sidebar Photoblog WordPress Plugin"><span style="color:#666">WordPress Sidebar Photoblog</span></a></small>
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
				'title'=>'Photoblog', 
				'category'=>'', 
				'numphoto'=>'3',
				'border'=>'1',
				'opacity'=>'1',
				'morelink'=>'',
				'hoverimage'=>'1',
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
		
		$size_w=get_option('thumbnail_size_w');
		$size_h=get_option('thumbnail_size_h');
		
		?>
        <p ><label for="sphoto-title"><?php _e('Title:'); ?><br /> <input class="widefat" id="sphoto-title" name="sphoto-title" type="text" value="<?php echo $title; ?>" style="width:200px;"/></label></p>
        		
		<p>
        <label><?php _e('Photoblog category'); ?><br />
         <?php
		if (!$category) echo '<strong>Currently: Nothing!</strong><br> Select a category as photoblog!<br />';?> 
        <?php wp_dropdown_categories('orderby=id&order=ASC&hide_empty=0&echo=1&selected='.$category.'&name=category');?></label>

        </p>
        
        <p>
        
        <label><?php _e('Number of photos'); ?>:
        <input type="text" name="numphoto" id="wnumphoto" value="<?php  echo $numphoto; ?>" style="width:40px;" /></label></p>
        
       
        <p>
        <?php _e('Thumbnail size'); ?>:<br/>
        <label>Width: &nbsp;<input type="text" name="size_w" id="wsize" value="<?php echo $size_w;?>" style="width:40px;" /> px</label><br />
        <label>Height: <input type="text" name="size_h" id="hsize" value="<?php echo $size_h;?>" style="width:40px;" /> px</label><br /></p>
        
    <label>More link (Automatically generated. Usually don't change it) <br />
    <input type="text" name="morelink" id="morelink" value="<?php echo $morelink; ?>" style="width:250px;" /><br />
</label><small>To hide more link leave it blank.</small>
    
        
   <p>     
</p><p>Other</p>

<input type="checkbox" name="border" id="border" value="1" <?php if ($border) echo 'checked'; ?> />
<label for="border">Display photos with border and padding (Recommended).
</label><br />

<input type="checkbox" name="opacity" id="opacity" value="1" <?php if ($opacity) echo 'checked'; ?> />
<label for="opacity">Enable opacity effect
</label><br />	

<input type="checkbox" name="hoverimage" id="hoverimage" value="1" <?php if ($hoverimage) echo 'checked'; ?> />
<label for="hoverimage">Display preview pop-up image (Great! but not recommended for more than 3 photos)
</label>


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
		
	$size_w=get_option('thumbnail_size_w');
	$size_h=get_option('thumbnail_size_h');
	
	
	 print "\n<!-- Start Sidebar Photoblog Wordpress http://wordpresswave.com/plugins/ -->\n";

	echo $before_widget . $before_title . $title . $after_title;
	?>
	<div class="widget_sphoto_body">

<?php  print_sphoto($category,$numphoto ,$size_w,$size_h,$hoverimage) ;

		if ($options['morelink']) echo '<br/><a href="'.$options['morelink'].'" title="Browse Photos">More Photos</a>';
		echo '</div>'.$after_widget;
		
	echo "\n<!-- End of Sidebar Photoblog Code -->\n";
	}
	
register_sidebar_widget('Sidebar Photoblog', 'widget_sphoto');
register_widget_control('Sidebar Photoblog', 'widget_sphoto_control', 345, 620);
}

//Don't worry if your theme isn't widgetized simply use this function : print_sphoto 
//Note: For Hoverimage make sure you enabled preview pop-up in the wdiget options because it needs some header code!
function print_sphoto($category,$numphoto=3,$size_w=100,$size_h=100,$hoverimage=false) {

	  $photos = new WP_Query('showposts='.$numphoto.'&cat='.$category); 
	  if ($photos->have_posts()) : 
	  	while ($photos->have_posts()) : $photos->the_post(); 
	  
			$post_id=get_the_ID();
			$imgsrc=sphoto_get_post_img_url($post_id,$size='thumbnail',$sizeinfo=false);
			
			if ($hoverimage){ $previewimage=sphoto_get_post_img_url($post_id,$size='medium',$sizeinfo=false);}
			// Testing $post_permalink=get_attachment_link($post_id);
		
			if ($imgsrc) { ?>
			
			<span><a href="<?php echo the_permalink(); ?>" rel="bookmark" <?php if (!$hoverimage) { echo 'title="';the_title_attribute();echo '"';} ?> ><img src="<?php echo $imgsrc; ?>" <?php if (!$hoverimage) { echo 'alt="';the_title_attribute();echo '"';} ?> width="<? echo $size_w; ?>px" height="<? echo $size_h;?>px" <?php if ($hoverimage) { ?> onmouseover="preview('<div class=\'preview_caption\'><img src=<?php echo $previewimage;?>><br/><center><?php the_title_attribute(); ?></center>');" onmouseout="hidepreview();"  <?php } ?>/> </a></span>
			
	 <?php } ?>
		  
   
	
	<?php
		 endwhile;
	
	 ?>
    <? 
	else : ?>
     <p class="center"><strong>No photo currently!</strong></p>
     <p> How can you add your photos here? Visit <a href="http://wordpresswave.com/plugins/sidebar-photoblog/">WordPress Sidebar Photoblog </a> or see Readme.txt in the plugin directory.</p>

	<?php 
	endif;
	
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




echo "\n<!-- Sidebar Photoblog Widget Style http://wordpresswave.com/plugins/ -->\n";	
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
		if  ($options['hoverimage']) { ?>
        <table style="position:absolute;top:-500px;left:0px;z-index:500;font-family:tahoma;font-size:11px;-moz-opacity:1;" id="preview" cellspacing=0 cellpadding=0>
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


/* Testing
function sphoto_post_filter($query) {
	global $parent_file, $wpdb, $wp_query;
	$options = get_option('widget_sphoto');
	
	if((isset($parent_file)||!empty($parent_file))){
		return;
	}
	
	if(is_feed()){
		if(isset($options['category']) && !empty($options['category'])){
			$wp_query->set('category__not_in',$options['category']);
		}		
	} else {
		
		if(is_home()){	
		$array=array($options['category']);
			if(isset($options['category']) && !empty($options['category'])){
				$wp_query->set('category__not',$options['category']);
			}
		}
	}
}
			
add_action('pre_get_posts','sphoto_post_filter');
*/




?>