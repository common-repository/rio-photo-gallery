<?php
/*
Plugin Name: rio-photo-gallery
Plugin URL: https://wordpress.org/plugins/rio-photo-gallery/
Description: A powerful photo gallery plugin that allows you to create a number of galleries and albums. We can easily add galleries using a single shortcode.
Version: 0.1
Author: Riosis Private Limited
Author URI: https://www.riosis.com/
*/
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
function rpgs_shortcode_script() {
  // wp_deregister_script( 'jquery' );
  // Change the URL if you want to load a local copy of jQuery from your own server.
  wp_register_style( 'rpgs-fluidVids.css',  plugins_url( 'css/demo.css', __FILE__ ));
  wp_register_style( 'rpgs-fluidVidsss.css',  plugins_url( 'css/riohalkaBox.min.css', __FILE__ ));
  wp_enqueue_style( 'rpgs-fluidVidsss.css' );
  wp_enqueue_style( 'rpgs-fluidVids.css');
}

function rpgsfooter_script(){
  wp_enqueue_script( 'rpgs-fluidVidss.js',  plugins_url( 'js/riohalkaBox.min.js', __FILE__ ),array( 'jquery' ));
  wp_enqueue_script( 'rpgs-fluidVidss.js' );
  ?>
  <script>
  jQuery(document).ready(function(){
    //put your js code here

    //  halkaBox.run("gallery1");
    halkaBox.run("riogallery2", {
      animation: "fade",
      theme: "dark",
      hideButtons: false,
      preload: 0
    });
    // /var lightbox =  new pureJSLightBox();
    var ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
    jQuery(function($) {
      jQuery(document).on('click', '.rioloadmoreimages', function() {
        var data = {
          'action': 'rpgs_loadimages',
          'page_no': jQuery('#page_no').val(),
          'data_limit':jQuery('#data_limit').val(),
          'album_id':jQuery('#album_id').val(),
          'gallery_id':jQuery('#gallery_id').val(),
          'security': '<?php echo wp_create_nonce("load_more_posts"); ?>'
        };
        jQuery.post(ajaxurl, data, function(response) {

          var obj=jQuery.parseJSON(response);
          if(obj.status){
            if(obj.hasmore){
              jQuery('.rioloadmoreimages').show();
            }else {
              jQuery('.rioloadmoreimages').hide();
            }
            var html='';
            jQuery.each(obj.records,function(index,value){
              if(obj.imgslider==1){
                html+=`<div class="col-md-`+obj.cols+` main-image-div">
                <a class="riogallery2" href="`+(obj.content_url)+`/rio_uploads/`+(value.image_path)+`" >
                <div class="image-div">
                <img src="`+value.image_path_url+`"" alt=""/>
                </div>
                </a> </div>`
              } else {
                html+=`<div class="col-md-`+obj.cols+` main-image-div">
                <a class="" href="`+(obj.content_url)+`/rio_uploads/`+(value.image_path)+`" >
                <div class="image-div">
                <img src="`+value.image_path_url`"" alt=""/>
                </div>
                </a> </div>`
              }
            })
            // lightbox.destroy();
            jQuery('#riogallery2').append(html);
            jQuery('#page_no').val(obj.next_page);
            element=document.getElementById('hb-wrapperriogallery2');
            element.parentNode.removeChild(element);
            window.setTimeout(function(){
              halkaBox.run("riogallery2", {
                animation: "fade",
                theme: "dark",
                hideButtons: false,
                preload: 0
              });
              //    lightbox =  new pureJSLightBox();
            },200);
          }
          else {
            jQuery('.rioloadmoreimages').hide();
          }
        });
      });
    });
  })
</script>
<script type="text/javascript">
jQuery('.load-more').click(function(e){
  jQuery(this).addClass('load-more--loading');
  setTimeout(function(e){
    jQuery('.load-more--loading').removeClass('load-more--loading')
  }, 3000);
})
</script>
<script type="text/javascript">
var ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
jQuery(function($) {
  jQuery(document).on('click', '.rioloadmore', function() {
    var data = {
      'action': 'rpgs_loadmore_ajax',
      'page_no': jQuery('#page_no').val(),
      'data_limit':jQuery('#data_limit').val(),
      'gallery_id':jQuery('#gallery_id').val(),
      'curtpage_id':jQuery('#curtpage_id').val(),
      'security': '<?php echo wp_create_nonce("load_more_posts"); ?>'
    };
    jQuery.post(ajaxurl, data, function(response) {
      var obj=jQuery.parseJSON(response);
      if(obj.status){
        if(obj.hasmore){
          jQuery('.rioloadmore').show();
        } else {
          jQuery('.rioloadmore').hide();
        }
        var html='';
        jQuery.each(obj.records,function(index,value){
          if(value.albumcount==0) {
            html+=`<div class="col-md-`+obj.cols+`">
            <a href="javascript:;">
            <div class="image-div">
            <img src="`+value.album_thumb+`" alt="no image" >
            </div>`;
            if(obj.img_count==1 ){
              html+=`<span class="p_count"> `+value.albumcount+` Photos </span>`;
            }
            if(obj.album_title==1) {
              html+= `<h5 class="albmTitle">`+value.album_name.replace(/\\/g, '')+`</h5>`;
            }
            html+=`</a>
            </div>`
          } else {
            html+=`<div class="col-md-`+obj.cols+`">
            <a href="`+obj.url+`?id=`+value.album_id+`&alb=`+encodeURI(value.album_name.replace(/\\/g, ''))+`">
            <div class="image-div">
            <img src="`+(value.album_thumb)+`" alt="no image" >
            </div>`;
            if(obj.img_count==1 ){
              html+=`<span class="p_count"> `+value.albumcount+` Photos </span>`;
            }
            if(obj.album_title==1) {
              html+= `<h5 class="albmTitle">`+value.album_name.replace(/\\/g, '')+`</h5>`;
            }
            html+=`</a>
            </div>`
          }
        })
        jQuery('#gallery_div').append(html);
        jQuery('#page_no').val(obj.next_page);
      }
      else {
        jQuery('.rioloadmore').hide();
      }
    });

  });
});
</script>
<?php
}
/* pagination for albums */
add_action('wp_ajax_rpgs_loadmore_ajax', 'rpgs_loadalbums');
add_action('wp_ajax_nopriv_rpgs_loadmore_ajax', 'rpgs_loadalbums');

function rpgs_loadalbums() {
  global $wpdb;
  global $wp;
  $table_album=$wpdb->prefix.'rio_album';
  $table_settings=$wpdb->prefix.'rio_settings';
  $table_images=$wpdb->prefix.'rio_image';

  $query2=$wpdb->prepare("SELECT * FROM $table_settings WHERE gallery_id=%s",intval($_POST['gallery_id']));
  $query_settings = $wpdb->get_results($query2);
  if(empty($query_settings)){
    $query_settings=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_settings WHERE gallery_id= %d",0));
  }
  if(!empty($_POST['page_no']) && !empty($_POST['data_limit']) && !empty($_POST['gallery_id'])){
    $from = intval($_POST['page_no'])*intval($_POST['data_limit']);
    $query2=$wpdb->prepare("SELECT *,(SELECT count(album_id) From $table_images WHERE $table_images.album_id=$table_album.album_id ) as albumcount FROM $table_album WHERE gallery_id=%s LIMIT $from,".intval($_POST['data_limit']),intval($_POST['gallery_id']) );
    $query_set = $wpdb->get_results($query2);
    $from2=$from+intval($_POST['data_limit']);
    $query3=$wpdb->prepare("SELECT *,(SELECT count(album_id) From $table_images WHERE $table_images.album_id=$table_album.album_id ) as albumcount FROM $table_album WHERE gallery_id=%s LIMIT $from2,".intval($_POST['data_limit']),intval($_POST['gallery_id']) );
    $query_set3 = $wpdb->get_results($query3);
    if(!empty($query_set)){
      if($query_set3){
        $result['hasmore']=true;
      }else {
        $result['hasmore']=false;
      }
      $result['status']=true;
      foreach ($query_set as $key => $value) {
        $query_set[$key]->album_thumb=rio_rpgs_manage_images($value->album_thumb,$query_settings[0]->set_album_img_width,$query_settings[0]->set_album_img_height);
      }
      $result['records']=$query_set;
      $result['next_page']=intval($_POST['page_no'])+1;
      $result['url']=sanitize_text_field($_POST['curtpage_id']);
      $result['plugin_url']=$url = plugin_dir_url( __FILE__ );
      $result['img_count']=$query_settings[0]->set_img_count;
      $result['album_title']=$query_settings[0]->set_albm_title;
      $result['width']=$query_settings[0]->set_album_img_width;
      $result['height']=$query_settings[0]->set_album_img_height;
      $num_col=$query_settings[0]->set_album_colmn;
      if($num_col==0){
        $num_col=1;
      }
      $col=12/$num_col;
      $col=floor($col);
      $result['cols']=$col;

    } else {
      $result['status']=false;
      $result['message']='No more records';
    }
  } else {
    $result['status']=false;
    $result['message']='Invalid Access';
  }
  echo json_encode($result);
  wp_die();
}
/* pagination for albums */

/* pagination for images */
add_action('wp_ajax_rpgs_loadimages', 'rpgs_loadimages_callback');
add_action('wp_ajax_nopriv_rpgs_loadimages', 'rpgs_loadimages_callback');

function rpgs_loadimages_callback() {
  global $wpdb;
  $table_images=$wpdb->prefix.'rio_image';
  $table_settings=$wpdb->prefix.'rio_settings';
  $query2=$wpdb->prepare("SELECT * FROM $table_settings WHERE gallery_id=%s",intval($_POST['gallery_id']) );
  $query_settings = $wpdb->get_results($query2);
  if(empty($query_settings)){
    $query_settings=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_settings WHERE gallery_id=%d",0));
  }
  if(!empty($_POST['page_no']) && !empty($_POST['data_limit']) && !empty($_POST['album_id'])){
    $from = intval($_POST['page_no'])*intval($_POST['data_limit']);
    $query_img=$wpdb->get_results("SELECT * FROM $table_images WHERE album_id='".intval($_POST['album_id'])."' LIMIT $from,".intval($_POST['data_limit']));
    $from2=$from+intval($_POST['data_limit']);
    $query_img2=$wpdb->get_results("SELECT * FROM $table_images WHERE album_id='".intval($_POST['album_id'])."' LIMIT $from2,".intval($_POST['data_limit']));
    if(!empty($query_img)){
      if($query_img2){
        $result['hasmore']=true;
      }else {
        $result['hasmore']=false;
      }
      $result['status']=true;
      foreach ($query_img as $key => $image) {
      $query_img[$key]->image_path_url=rio_rpgs_manage_images($image->image_path,$query_settings[0]->set_img_width,$query_settings[0]->set_img_height);
      }
      $result['records']=$query_img;
      $result['next_page']=intval($_POST['page_no'])+1;
      $base = dirname(__FILE__);
      $result['plugin_url']=$url = plugin_dir_url( __FILE__ );
      $result['content_url']=$url =content_url();
      $result['width']=$query_settings[0]->set_img_width ;
      $result['height']=$query_settings[0]->set_img_height;
      $result['imgslider']=$query_settings[0]->set_img_slider;
      $num_col=$query_settings[0]->set_img_column ;
      if($num_col==0){
        $num_col=1;
      }
      $col=12/$num_col;
      $col=floor($col);
      $result['cols']=$col;
    } else {
      $result['status']=false;
      $result['message']='No more records';
    }
  } else {
    $result['status']=false;
    $result['message']='Invalid Access';
  }
  echo json_encode($result);
  wp_die();
}

/* pagination for images */

add_action( 'wp_enqueue_scripts', 'rpgs_shortcode_script' );

/* display gallery for frod end */
function rpgs_gallery( $atts ) {
  if(!is_admin() && empty($_GET['_locale'])){
  add_action('wp_footer', 'rpgsfooter_script');
  global $wp;
  $url = plugin_dir_url( __FILE__ );
  $filename=Date('F Y');
  global $wpdb;
  $table_gallery=$wpdb->prefix.'rio_album';
  $table_settings=$wpdb->prefix.'rio_settings';
  $table_images=$wpdb->prefix.'rio_image';
  $query2=$wpdb->prepare("SELECT * FROM $table_settings WHERE gallery_id=%s",intval($atts['id']) );
  $query_set = $wpdb->get_results($query2);

  $count=$wpdb->get_results("SELECT count(gallery_id) as count From $table_gallery WHERE gallery_id=".intval($atts['id']));
  $count=json_decode(json_encode($count),true)[0]['count'];
  if(empty($query_set)){
    $query_set=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_settings WHERE gallery_id=%d",0));
  }
  $query=$wpdb->prepare("SELECT * ,(SELECT Count(album_id) FROM $table_images WHERE $table_images.album_id=$table_gallery.album_id) as albumcount FROM $table_gallery WHERE gallery_id=%s LIMIT 0,".$query_set[0]->set_album_per_page,$atts['id']);
  $galleries = $wpdb->get_results($query);
  if(empty($query_set[0]->set_albm_ajx_paginatn)) {
    $query=$wpdb->prepare("SELECT * ,(SELECT Count(album_id) FROM $table_images WHERE $table_images.album_id=$table_gallery.album_id) as albumcount FROM $table_gallery WHERE gallery_id=%s",$atts['id'] );
    $galleries = $wpdb->get_results($query);
  }
  if(empty($_GET['id']) && empty($_GET['alb'])) {
    ?>
    <div class="row gallery_plugin" id="gallery_div">
      <?php
      $num_col=$query_set[0]->set_album_colmn;
      if($num_col==0){
        $num_col=1;
      }
      $col=12/$num_col;
      $col=floor($col);
      if(!empty($galleries)) {
        foreach ( $galleries as $gallery ) {
          if($gallery->albumcount==0){
            ?>
            <div class="col-md-<?php echo $col; ?>">
              <a href="javascript:;">
                <div class="image-div">
                  <img src="<?php echo rio_rpgs_manage_images($gallery->album_thumb,$query_set[0]->set_album_img_width,$query_set[0]->set_album_img_height );?>" alt="no image" >
                </div>
                <?php if($query_set[0]->set_img_count==1){ ?>
                  <span class="p_count"><?php echo $gallery->albumcount; ?> Photos</span> <?php } ?>
                  <?php if($query_set[0]->set_albm_title==1){ ?>
                    <h5 class="albmTitle"><?php echo stripslashes($gallery->album_name); ?></h5> <?php } ?>
                  </a>
                </div> <?php } else {  ?>
                  <div class="col-md-<?php echo $col; ?>">
                    <a href="<?php echo home_url( $wp->request );?>?id=<?php echo $gallery->album_id;?>&alb=<?php echo urlencode(stripslashes($gallery->album_name)); ?>">
                      <div class="image-div">
                        <img src="<?php echo rio_rpgs_manage_images($gallery->album_thumb,$query_set[0]->set_album_img_width,$query_set[0]->set_album_img_height );?>" alt="no image" >
                      </div>
                      <?php if($query_set[0]->set_img_count==1){ ?>
                        <span class="p_count"><?php echo $gallery->albumcount; ?> Photos</span> <?php } ?>
                        <?php if($query_set[0]->set_albm_title==1){ ?>
                          <h5 class="albmTitle"><?php echo stripslashes($gallery->album_name); ?></h5> <?php  } ?>
                        </a>
                      </div>
                    <?php }  } ?>
                  </div>
                  <?php
                  if($query_set[0]->set_albm_ajx_paginatn==1) {
                    if($query_set[0]->set_album_per_page < $count){
                      //echo $count;
                      ?>
                      <div class="clearfix"> <a href="javascript:;" class="rioloadmore load-more">Load More ...</a></div>
                    <?php } }  ?>
                  <?php } else { ?>
                    <div class="">
                      Albums Not Found
                    </div>
                  <?php } ?>
                  <form autocomplete="off">
                    <input type="hidden" id='data_limit' name="" value="<?php echo $query_set[0]->set_album_per_page; ?>">
                    <input type="hidden" id='page_no' name="" value="1">
                    <input type="hidden" id='gallery_id' name="" value="<?php echo $atts['id']; ?>">
                    <input type="hidden" id='curtpage_id' name="" value="<?php echo home_url($wp->request); ?>">
                  </form>
                <?php  } else {
                  $num_colimg=$query_set[0]->set_img_column;
                  $img_slider=$query_set[0]->set_img_slider;
                  if($num_colimg==0){
                    $num_colimg=1;
                  }
                  $num_colimg=12/$num_colimg;
                  $num_colimg=floor($num_colimg);
                  ?>
                  <div class="bread-crumb plugin_breadcrumb">
                    <a href=" <?php echo home_url( $wp->request); ?>"> <?php the_title(); ?> </a> >> <?php echo stripslashes($_GET['alb']);
                    $query_img=$wpdb->get_results("SELECT * FROM $table_images WHERE album_id='".intval($_GET['id'])."' LIMIT 0,".$query_set[0]->set_img_per_page);
                    $img_count=$wpdb->get_results("SELECT count(album_id) as imgcount From $table_images WHERE album_id=".intval($_GET['id']));
                    $img_count=json_decode(json_encode($img_count),true)[0]['imgcount'];
                    //  print_r($img_count);
                    if(empty($query_set[0]->set_img_ajx_paginatn)) {
                      $query_img=$wpdb->get_results("SELECT * FROM $table_images WHERE album_id='".intval($_GET['id'])."'");
                    }
                    ?>
                  </div>
                  <div id="riogallery2" class="wrapper row gallery_photos gallery_plugin">
                    <?php
                    if(!empty($query_img)){
                      foreach ($query_img as $images){
                        ?>
                        <div class="col-md-<?php echo $num_colimg; ?> main-image-div">
                          <?php if($img_slider==1){
                            ?>
                            <a href="<?php echo content_url(); ?>/rio_uploads/<?php echo $images->image_path; ?>" class="riogallery2">
                              <div class="image-div">
                                <img src="<?php echo rio_rpgs_manage_images($images->image_path,$query_set[0]->set_img_width,$query_set[0]->set_img_height );?>" alt="" />
                              </div>
                            </a> <?php } else { ?>
                              <a href="<?php echo content_url(); ?>/rio_uploads/<?php echo $images->image_path; ?>" class="">
                                <div class="image-div">
                                  <img src="<?php echo rio_rpgs_manage_images($images->image_path,$query_set[0]->set_img_width,$query_set[0]->set_img_height );?>" alt="" />
                                </div>
                              </a>
                            <?php } ?>
                          </div>
                        <?php } ?>
                      </div>
                      <?php
                      if($query_set[0]->set_img_ajx_paginatn==1) {
                        if($query_set[0]->set_img_per_page < $img_count) {
                          ?>
                          <div class="clearfix">
                            <a href="javascript:;" class="rioloadmoreimages">Load More...</a>
                          </div> <?php } } ?>
                        <?php } else { ?>
                          <div class="">
                            Images not found
                          </div>
                        <?php } ?>
                        <form autocomplete="off">
                          <input type="hidden" id='data_limit' name="" value="<?php echo $query_set[0]->set_img_per_page; ?>">
                          <input type="hidden" id='page_no' name="" value="1">
                          <input type="hidden" id='album_id' name="" value="<?php echo $_GET['id']; ?>">
                          <input type="hidden" id='gallery_id' name="" value="<?php echo $atts['id']; ?>">
                        </form>

                        <?php
                      } ?>
                      <?php
                    }
                    }
                    add_shortcode( 'rio-gallery', 'rpgs_gallery' );

                    /* display gallery for frod end */

                    // create table while activating the plugin
                    date_default_timezone_set('Asia/Kolkata');
                    global $jal_db_version;
                    $jal_db_version = '1.0';
                    function on_activation() {
                      ob_start();
                      global $wpdb;
                      global $jal_db_version;
                      $table_gallery = $wpdb->prefix . 'rio_gallery';
                      $table_album = $wpdb->prefix . 'rio_album';
                      $table_image = $wpdb->prefix . 'rio_image';
                      $table_settings = $wpdb->prefix . 'rio_settings';
                      $charset_collate = $wpdb->get_charset_collate();
                      #Check to see if the table exists already, if not, then create it
                      $sql = "CREATE TABLE IF NOT EXISTS $table_gallery (
                        gallery_id mediumint(9) NOT NULL AUTO_INCREMENT,
                        gallery_name varchar(200) DEFAULT '' NOT NULL,
                        gallery_slug varchar(200) DEFAULT '' NOT NULL,
                        created_at datetime  NOT NULL,
                        updated_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP,
                        PRIMARY KEY  (gallery_id)
                      ) $charset_collate;";

                      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                      dbDelta( $sql );
                      #Check to see if the table exists already, if not, then create it
                      $sql_album = "CREATE TABLE IF NOT EXISTS $table_album (
                        album_id mediumint(9) NOT NULL AUTO_INCREMENT,
                        gallery_id int NOT NULL,
                        album_name varchar(100) DEFAULT '' NOT NULL,
                        album_slug varchar(100) DEFAULT '' NOT NULL,
                        album_thumb varchar(255) DEFAULT '' NOT NULL,
                        created_at datetime  NOT NULL,
                        updated_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP,
                        PRIMARY KEY  (album_id)
                      ) $charset_collate;";

                      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                      dbDelta( $sql_album );
                      #Check to see if the table exists already, if not, then create it
                      $sql_img = "CREATE TABLE IF NOT EXISTS $table_image (
                        image_id mediumint(9) NOT NULL AUTO_INCREMENT,
                        gallery_id int NOT NULL,
                        album_id int NOT NULL,
                        image_path varchar(255) DEFAULT '' NOT NULL,
                        created_at datetime NOT NULL,
                        updated_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP,
                        PRIMARY KEY  (image_id)
                      ) $charset_collate;";

                      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                      dbDelta( $sql_img );

                      #Check to see if the table exists already, if not, then create it
                      $sql_settings = "CREATE TABLE IF NOT EXISTS $table_settings (
                        set_id mediumint(9) NOT NULL AUTO_INCREMENT,
                        gallery_id int NOT NULL,
                        set_album_img_width int NOT NULL,
                        set_album_img_height int NOT NULL,
                        set_album_per_page int NOT NULL,
                        set_album_colmn int NOT NULL,
                        set_img_count tinyint NOT NULL  DEFAULT 0,
                        set_albm_title tinyint NOT NULL  DEFAULT 0,
                        set_albm_ajx_paginatn tinyint NOT NULL  DEFAULT 0,
                        set_img_width int NOT NULL,
                        set_img_height int NOT NULL,
                        set_img_per_page int NOT NULL,
                        set_img_column int NOT NULL,
                        set_img_ajx_paginatn tinyint NOT NULL  DEFAULT 0,
                        set_img_slider tinyint NOT NULL  DEFAULT 0,
                        created_at datetime NOT NULL,
                        updated_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP,
                        PRIMARY KEY  (set_id)
                      ) $charset_collate;";
                      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                      dbDelta( $sql_settings );
                      $wpdb->insert($table_settings,array(
                        'set_id' => 1,
                        'gallery_id'=>0,
                        'set_album_img_width'=>300 ,
                        'set_album_img_height'=> 200 ,
                        'set_album_per_page' =>16 ,
                        'set_album_colmn'=>4,
                        'set_img_count'=>1,
                        'set_albm_title'=>1,
                        'set_albm_ajx_paginatn'=>1,
                        'set_img_width'=>300,
                        'set_img_height'=>200,
                        'set_img_per_page'=>16,
                        'set_img_column'=>4,
                        'set_img_ajx_paginatn'=>1,
                        'set_img_slider'=>1,
                        'created_at' => date('Y:m:d H:i:s')
                      )
                    );
                    add_option( 'jal_db_version', $jal_db_version );
                    ob_flush();
                  }
                  function on_uninstall(){
                    global $wpdb;
                    $table_gallery = $wpdb->prefix . 'rio_gallery';
                    $table_album = $wpdb->prefix . 'rio_album';
                    $table_image = $wpdb->prefix . 'rio_image';
                    $table_settings = $wpdb->prefix . 'rio_settings';
                    $sql = "DROP TABLE IF EXISTS $table_gallery;";
                    $wpdb->query($sql);
                    $sql_album = "DROP TABLE IF EXISTS $table_album;";
                    $wpdb->query($sql_album);
                    $table_image = "DROP TABLE IF EXISTS $table_image;";
                    $wpdb->query($table_image);
                    $table_settings = "DROP TABLE IF EXISTS $table_settings;";
                    $wpdb->query($table_settings);
                    delete_option("jal_db_version");
                    $dir=dirname( __FILE__).'/uploads';
                    delTree($dir);
                  }
                  function delTree($dir) {
                    $files = array_diff(scandir($dir), array('.','..'));
                    foreach ($files as $file) {
                      (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
                    }
                    $folders=explode('/',$dir);
                    $folders=end($folders);
                    if($folders!='uploads'){
                      return rmdir($dir);
                    }
                  }
                  register_activation_hook( __FILE__, 'on_activation' );
                  register_uninstall_hook(__FILE__, 'on_uninstall');
                  //register_deactivation_hook(__FILE__, 'on_deactivation');

                  //enable js file

                  function rpgs_wptuts_scripts_basic()
                  {

                    // Register the script like this for a plugin:
                    wp_enqueue_script( 'rpgs-custom-script',  plugins_url( '/js/riogalleryscripts.js', __FILE__ ) );
                    wp_enqueue_script( 'rpgs-custom-script1', plugins_url( '/js/dataTables.bootstrap.min.js', __FILE__ ) );
                    wp_enqueue_script( 'rpgs-custom-script2', plugins_url( '/js/jquery.dataTables.min.js', __FILE__ ) );
                    wp_enqueue_script( 'rpgs-custom-script5', plugins_url( '/js/bootstrap.min.js', __FILE__ ) );
                    wp_enqueue_script( 'rpgs-custom-script6', plugins_url( '/js/bootstrap-notify.js', __FILE__ ) );
                  }
                  add_action( 'admin_enqueue_scripts', 'rpgs_wptuts_scripts_basic' );

                  // enable css style
                  function rpgs_wpse_load_plugin_css() {
                    $plugin_url = plugin_dir_url( __FILE__ );
                    wp_enqueue_style( 'rpgs-style1', $plugin_url . 'css/styles.css' );
                    wp_enqueue_style( 'rpgs-style2', $plugin_url . 'css/dataTables.bootstrap.min.css' );
                    wp_enqueue_style( 'rpgs-style3', $plugin_url . 'css/jquery.dataTables.min.css' );

                    wp_register_style('prefix_bootstrap', $plugin_url. 'css/bootstrap.min.css');
                    wp_enqueue_style('prefix_bootstrap');

                  }
                  if(!empty($_GET['page'])){
                    if(in_array($_GET['page'],['manage-albums','manage-options','add-albums','add-gallery','rio-photo-gallery','edit-album','individual-albums','individual-create-albums'])){
                      add_action( 'admin_enqueue_scripts', 'rpgs_wpse_load_plugin_css' );
                    }
                  }
                  /* add menu */
                  add_action('admin_menu', 'rpgs_gallery_menu');
                  function rpgs_gallery_menu(){
                    add_menu_page( 'Rio Photo Gallery Page', 'Rio Photo Gallery ', 'manage_options', 'rio-photo-gallery', 'rio_rpgs_gallery_init','dashicons-format-gallery' );
                    add_submenu_page('rio-photo-gallery', 'Add Gallery' ,'Add Gallery', 'manage_options','add-gallery','rio_rpgs_gallery' );
                    add_submenu_page('rio-photo-gallery', 'Add Albums' ,'Add Albums', 'manage_options','add-albums','rio_rpgs_albums' );
                    add_submenu_page('rio-photo-gallery', 'Manage Albums' ,'Manage Albums', 'manage_options','manage-albums','rio_rpgs_manage_albums' );
                    add_submenu_page('rio-photo-gallery', 'Manage Options' ,'Gallery Settings', 'manage_options','manage-options','rio_rpgs_manage_options' );
                    add_submenu_page(null, 'Manage Image' ,'Image Settings', 'manage_options','manage-images','rio_rpgs_manage_images' );
                    add_submenu_page(null, 'Edit Album' ,'', 'manage_options','edit-album','rio_rpgs_edit_albums' );
                    add_submenu_page(null, 'Individual Albums' ,'', 'manage_options','individual-albums','rio_rpgs_individual_albums' );
                    add_submenu_page(null, 'Individual Create Albums' ,'', 'manage_options','individual-create-albums','rio_rpgs_individual_create_albums' );
                  }

                  //for delete gallery by individual
                  add_action( 'admin_footer', 'rpgs_dlt_galleryaction' ); // Write our JS below here
                  function rpgs_dlt_galleryaction() { ?>
                    <script type="text/javascript">
                    jQuery(document).ready(function($) {
                      jQuery(".gallerydlt").click(function(){
                        var data = {
                          'action': 'rpgs_dltaction_gallery',
                          'id': jQuery('#delete_id').val(),
                          'galry_date': jQuery('#galry_date').val(),
                          'cret_nonce': jQuery('#cret_nonce').val(),
                          //'id': this.id,
                        };
                        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                        jQuery.post(ajaxurl, data, function(response) {
                          var obj=$.parseJSON(response);
                          if(obj.status){

                            $.notify({
                              // options
                              message: 'Successfully Deleted'
                            },{
                              // settings
                              type: 'success'
                            });

                            //  alert('ture');
                            window.setTimeout(function(){
                              location.reload();
                            },500)
                          } else {
                            //alert('false');
                            $.notify({
                              // options
                              message: 'Could not delete'
                            },{
                              // settings
                              type: 'danger'
                            });
                          }
                          window.setTimeout(function(){
                            location.reload();
                          },500)
                        });
                      });
                    });
                    </script> <?php
                  }

                  add_action( 'wp_ajax_rpgs_dltaction_gallery', 'rpgs_dltaction_gallery' );
                  function rpgs_dltaction_gallery() {
                    global $wpdb;
                    $gallery_id = intval( $_POST['id'] );
                    $tablename=$wpdb->prefix.'rio_gallery';
                    if(wp_verify_nonce($_POST['cret_nonce'],'rpgs-nonce'.$_POST['galry_date'])){
                      $response=$wpdb->delete( $tablename, [ 'gallery_id' => $gallery_id ], [ '%d' ] );
                      if($response>0) {
                        $result['status']=true;
                      } else {
                        $result['status']=false;
                      }
                    } else {
                      $result['status']=false;
                      $result['message']='Invalid access';
                    }

                    echo json_encode($result);
                    wp_die(); // this is required to terminate immediately and return a proper response
                  }
                  //for edit gallery


                  //for delete albums by individual
                  add_action( 'admin_footer', 'album_delete_script' ); // Write our JS below here
                  function album_delete_script() { ?>
                    <script type="text/javascript">
                    jQuery(document).ready(function($) {
                      jQuery(".albumdlt").click(function(){
                        var data = {
                          'action': 'album_delete_action',
                          'id': this.id,
                          'galry_date': jQuery('#galry_date').val(),
                          'cret_nonce': jQuery('#cret_nonce').val(),
                        };
                        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                        jQuery.post(ajaxurl, data, function(response) {
                          var obj=$.parseJSON(response);
                          if(obj.status){
                            $.notify({
                              // options
                              message: 'Successfully Deleted'
                            },{
                              // settings
                              type: 'success'
                            });

                            //  alert('ture');
                            window.setTimeout(function(){
                              location.reload();
                            },1000)
                          } else {
                            //alert('false');
                            $.notify({
                              // options
                              message: 'Could not delete'
                            },{
                              // settings
                              type: 'danger'
                            });
                            window.setTimeout(function(){
                              location.reload();
                            },1000)
                          }
                        });
                      });
                    });
                    </script> <?php
                  }

                  add_action( 'wp_ajax_album_delete_action', 'album_delete_action' );
                  function album_delete_action() {
                    global $wpdb; // this is how you get access to the database
                    $album_id = intval( $_POST['id'] );
                    $tablename=$wpdb->prefix.'rio_album';
                    $tableimage=$wpdb->prefix.'rio_image';
                    if(wp_verify_nonce($_POST['cret_nonce'],'rpgs-nonce'.$_POST['galry_date'])){
                      $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $tablename WHERE album_id=%d",$album_id));
                      $files=(WP_CONTENT_DIR.'/rio_uploads/'.$results[0]->album_thumb ); //get file names
                      if(!empty($files)) {
                        unlink($files); // delete file from the folder
                      }
                      $imgresults = $wpdb->get_results($wpdb->prepare("SELECT * FROM $tableimage WHERE album_id=%d",$album_id));
                      foreach ($imgresults as $image) {
                        $img_file=WP_CONTENT_DIR.'/rio_uploads/'.$image->image_path;
                        if(!empty($img_file)){
                          unlink($img_file);
                        } }
                        $wpdb->delete( $tableimage, [ 'album_id' => $album_id ], [ '%d' ] );
                        $response=$wpdb->delete( $tablename, [ 'album_id' => $album_id ], [ '%d' ] );
                        if($response>0) {
                          $result['status']=true;
                        } else {
                          $result['status']=false;
                        }
                      }else {
                        $result['status']=false;
                        $result['message']='Invalid access';
                      }
                      echo json_encode($result);
                      wp_die(); // this is required to terminate immediately and return a proper response
                    }

                    // delete all albums

                    add_action( 'admin_footer', 'all_album_delete_script' ); // Write our JS below here
                    function all_album_delete_script() { ?>
                      <script type="text/javascript">
                      jQuery(document).ready(function($) {
                        jQuery(".all-albmdlt").click(function(){
                          var data = {
                            'action': 'allalbum_delete_action',
                          };
                          // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                          jQuery.post(ajaxurl, data, function(response) {
                            var obj=$.parseJSON(response);
                            if(obj.status){
                              $.notify({
                                // options
                                message: 'Successfully Deleted'
                              },{
                                // settings
                                type: 'success'
                              });

                              //  alert('ture');
                              window.setTimeout(function(){
                                location.reload();
                              },1000)
                            } else {
                              //alert('false');
                              $.notify({
                                // options
                                message: 'Could not delete'
                              },{
                                // settings
                                type: 'danger'
                              });
                              window.setTimeout(function(){
                                location.reload();
                              },1000)
                            }
                          });
                        });
                      });
                      </script> <?php
                    }

                    add_action( 'wp_ajax_all_album_delete_action', 'allalbum_delete_action' );
                    function allalbum_delete_action() {
                      global $wpdb; // this is how you get access to the database
                      $tablename=$wpdb->prefix.'rio_album';
                      $response= $wpdb->query($wpdb->prepare("DELETE * from $tablename "));
                      // return ##
                      $return = __('Table Emptied.');
                      if ( $wpdb->last_error ) {
                        $return = $wpdb->last_error;
                      }
                      wp_die(); // this is required to terminate immediately and return a proper response
                    }

                    //for delete album  images by individual
                    add_action( 'admin_footer', 'album_imgdlt_script' ); // Write our JS below here
                    function album_imgdlt_script() { ?>
                      <script type="text/javascript">
                      jQuery(document).ready(function($) {
                        jQuery(".imgdlt").click(function(){
                          var data = {
                            'action': 'album_img_delete_action',
                            'id': this.id,
                            'galry_date': jQuery('#galry_date').val(),
                            'cret_nonce': jQuery('#cret_nonce').val(),
                          };
                          // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                          jQuery.post(ajaxurl, data, function(response) {
                            var obj=$.parseJSON(response);
                            if(obj.status){
                              $.notify({
                                // options
                                message: 'Successfully Deleted'
                              },{
                                // settings
                                type: 'success'
                              });

                              //  alert('ture');
                              window.setTimeout(function(){
                                location.reload();
                              },1000)
                            } else {
                              //alert('false');
                              $.notify({
                                // options
                                message: 'Could not delete'
                              },{
                                // settings
                                type: 'danger'
                              });
                              window.setTimeout(function(){
                                location.reload();
                              },1000)
                            }
                          });
                        });
                      });
                      </script> <?php
                    }

                    add_action( 'wp_ajax_album_img_delete_action', 'album_img_delete_action' );
                    function album_img_delete_action() {
                      global $wpdb; // this is how you get access to the database
                      $image_id = intval( $_POST['id'] );
                      $tablename=$wpdb->prefix.'rio_image';
                      if(wp_verify_nonce($_POST['cret_nonce'],'rpgs-nonce'.$_POST['galry_date'])){
                        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $tablename WHERE image_id=%d",$image_id));
                        $files=WP_CONTENT_DIR.'/rio_uploads/'.$results[0]->image_path;
                        if(!empty($files)) {
                          unlink($files);
                        }
                        $response=$wpdb->delete( $tablename, [ 'image_id' => $image_id ], [ '%d' ] );
                        if($response>0) {
                          $result['status']=true;
                        } else {
                          $result['status']=false;
                        }
                      } else {
                        $result['status']=false;
                        $result['message']='Inavalid Access';
                      }
                      echo json_encode($result);
                      wp_die(); // this is required to terminate immediately and return a proper response
                    }

                    function rio_rpgs_gallery_init(){ ?>

                      <div class="">
                        <h3> Overview</h3> <br>
                        <h5><b>  Description : </b></h5>
                        A powerful photo gallery plugin that allows you to create a number of galleries and albums. We can easily add galleries using a single shortcode. <br>
                        <h5><b> How it Works : </b></h5>
                        <ul>
                          <li> Step 1 : Activate the Plugin </li>
                          <li> Step 2 : You can see the option 'Rio Photo Gallery' on the left side of the menubar </li>
                          <li> Step 3 : To create galleries, Go to Add Gallery -> Fill the name of the gallery  </li>
                          <li>(Note: A shortcode is generated when creates a gallery. This shortcode will be used to add galleries to your site.) </li>
                          <li> Step 4 : To create albums under the galleries, Go to Add Albums -> Fill all fields</li>
                          <li> Step 5 : To create a new page, Go to Pages -> Add New</li>
                          <li> Step 6 : Copy the shortcode and paste in the content area of the page</li>
                        </ul>
                      </div>

                      <!-- <iframe width="1000" height="500" src="https://www.youtube.com/embed/gbRcE2AeNFg" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe> -->
                    <?php }
                    require_once('rio-gallery.php');
                    require_once('rio-album.php');
                    require_once('rio-manage-options.php');

                    function rio_rpgs_manage_images($url,$given_width,$given_height)
                    {
                      $url=sanitize_text_field($url);
                      $imgSrc=WP_CONTENT_DIR.'/rio_uploads/'.$url;
                      list($width, $height) = getimagesize($imgSrc);
                      $data=file_get_contents($imgSrc);
                      $myImage = imagecreatefromstring($data);
                      // calculating the part of the image thumbnail
                      if ($width > $height)
                      {
                        $y = 0;
                        $x = ($width - $height) / 2;
                        $smallestSide = intval($height);
                      }
                      else
                      {
                        $x = 0;
                        $y = ($height - $width) / 2;
                        $smallestSide = intval($width);
                      }
                      // copying the part into thumbnail
                      if(!empty($given_width)){
                        $new_width = intval($given_width);
                      }
                      else {
                        $new_width=intval($width);
                      }
                      if(!empty($given_height)){

                        $new_height=intval($given_height);
                      }
                      else {
                        $new_height=intval($height);
                      }
                      $thumb = imagecreatetruecolor($new_width, $new_height);
                      imagealphablending($thumb, false);
                      imagesavealpha($thumb, true);
                      imagecopyresampled($thumb, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

                      //final output

                      $background = imagecolorallocate($thumb , 0, 0, 0);
                      imagecolortransparent($thumb, $background);
                      $index = imagecolorat($thumb, 0, 0);
                      $rgb = imagecolorsforindex($thumb, $index);

                      $type=mime_content_type($imgSrc);
                      if ($type == "image/jpeg" || $type == "image/jpg"|| $type == "jpg" ) {
                        ob_start ();
                        imagejpeg($thumb,null,100);
                        $image_data = ob_get_contents ();
                        ob_end_clean ();
                        $image_data_base64 = base64_encode ($image_data);
                        return getDataURI($image_data_base64,$type);
                      }
                      else if ($type == "image/x-png" || $type == "image/png"  || $type == "png") {
                        ob_start ();
                        imagepng($thumb);
                        $image_data = ob_get_contents ();
                        ob_end_clean ();
                        $image_data_base64 = base64_encode ($image_data);
                        return getDataURI($image_data_base64,$type);

                      }
                      else if ($type == "image/gif" || $type=='gif') {
                        ob_start ();
                        imagegif($thumb);
                        $image_data = ob_get_contents ();
                        ob_end_clean ();
                        $image_data_base64 = base64_encode ($image_data);
                        return getDataURI($image_data_base64,$type);

                      }
                    }
                    function getDataURI($image, $mime = '') {
                    	return 'data: '. $mime.';base64,'.$image;
                    }
                      ?>
