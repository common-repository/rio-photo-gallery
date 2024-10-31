<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
function rio_rpgs_manage_options(){ ?>
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <style media="screen">
  option[disabled] {
    display: none;
  }
  </style>
  <body>
    <h2  class="wp-heading-inline mntitle1">Gallery Settings</h2>
    <div class="clearfix">
    </div>
    <div class="wrap">
      <div class="panel-group">
        <div class="panel panel-default">
          <div class="panel-heading"><strong>Gallery Settings</strong></div>
          <div class="panel-body">
            <form class="form-horizontral row" action="" method="post" id="settingsform">
              <div class="form-group">
                <label class="col-sm-3" for="">Select Gallery </label>
                <div class="col-sm-4">
                  <select class="form-control" name="albm_gallery" id="set_gallery">
                    <option value="" disabled selected >Select Gallery</option>
                    <?php

                    global $wpdb;
                    $table_gallery=$wpdb->prefix.'rio_gallery';
                    $gallery=$wpdb->prepare("SELECT * FROM $table_gallery",[]);
                    $galleries=$wpdb->get_results($gallery);
                    ?>
                    <?php foreach ($galleries as $gallery){
                      ?>
                        <option value="<?php echo $gallery->gallery_id; ?>"><?php echo stripslashes($gallery->gallery_name); ?></option>
                      <?php }  ?>
                    </select>
                  </div>
                </div>
                <div class="clearfix">
                </div>
                <div id="setings" class="col-sm-12 m-md-top" style="display:none;">
                  <label class="m-sm-btm bxTitle" for="">Album Settings</label>
                  <br>
                  <small class="m-sm-btm"></small>
                  <table class="vertop form-table">
                    <tbody>
                      <tr>
                        <th>Album Preview Image Size</th>
                        <td>
                          <fieldset>
                            <label class="thumbnail_size_w" for="">Width</label>
                            <input class="rionumbertext" maxlength="10" type="text" name="album_img_width" value="">
                            <br>
                            <label class="thumbnail_size_w" for="">Height</label>
                            <input class="rionumbertext" maxlength="10" type="text" name="album_img_height" value="">
                          </fieldset>
                        </td>
                      </tr>
                      <tr>
                        <th> Albums Per Page</th>
                        <td>
                          <fieldset>
                            <label class="thumbnail_size_w" for=""></label>
                            <input class="rionumbertext" maxlength="5" type="text" name="album_per_page" value="">
                          </fieldset>
                        </td>
                      </tr>
                      <tr>
                        <th>Display Number of Columns </th>
                        <td>
                          <fieldset>
                            <label class="thumbnail_size_w" for=""></label>
                            <input class="rionumbertext" maxlength="3" type="text" name="album_colmn" value="">
                          </fieldset>
                        </td>
                      </tr>
                      <tr>
                        <th>Show Image Count</th>
                        <td>
                          <fieldset>
                            <label class="thumbnail_size_w"></label>
                            <input type="checkbox" name="album_img_count" value="1" checked="checked"> YES
                          </fieldset>
                        </td>
                      </tr>
                      <tr>
                        <th>Show Album Title</th>
                        <td>
                          <fieldset>
                            <label class="thumbnail_size_w"></label>
                            <input type="checkbox" name="album_title" value="1" checked="checked"> YES
                          </fieldset>
                        </td>
                      </tr>
                      <tr>
                        <th>Show Ajax Pagination</th>
                        <td>
                          <fieldset>
                            <label class="thumbnail_size_w"></label>
                            <input type="checkbox" name="ajx_pagination" value="1" checked="checked"> YES
                          </fieldset>
                        </td>
                      </tr>

                    </tbody>
                  </table>
                  <label class="m-sm-btm bxTitle" for="">Image Settings</label> <br>
                  <table class="vertop form-table">
                    <tbody>
                      <tr>
                        <th>Image Size</th>
                        <td>
                          <fieldset>
                            <label class="thumbnail_size_w" for="">Width</label>
                            <input class="rionumbertext" maxlength="100" type="text" name="img_width" value="">
                            <br>
                            <label class="thumbnail_size_w" for="">Height</label>
                            <input class="rionumbertext" maxlength="100" type="text" name="img_height" value="">
                          </fieldset>
                        </td>

                      </tr>
                      <tr>
                        <th> Images Per Page</th>
                        <td>
                          <fieldset>
                            <label class="thumbnail_size_w" for=""></label>
                            <input class="rionumbertext" maxlength="5" type="text" name="img_per_page" value="">
                          </fieldset>
                        </td>
                      </tr>
                      <tr>
                        <th>Display Number of Columns </th>
                        <td>
                          <fieldset>
                            <label class="thumbnail_size_w" for=""></label>
                            <input class="rionumbertext" maxlength="3" type="text" name="img_column" value="">
                          </fieldset>
                        </td>
                      </tr>
                      <tr>
                        <th>Show Ajax Pagination</th>
                        <td>
                          <fieldset>
                            <label class="thumbnail_size_w"></label>
                            <input type="checkbox" name="img_pagnation" value="1" checked="checked"> YES
                          </fieldset>
                        </td>
                      </tr>
                      <tr>
                        <th>Activate Image Slider</th>
                        <td>
                          <fieldset>
                            <label class="thumbnail_size_w"></label>
                            <input type="checkbox" name="set_img_slider" value="1" checked="checked"> YES
                          </fieldset>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <?php
                  $date=Date('Y-m-d H:i:s');
                  $once= wp_create_nonce('rpgs-nonce'.$date);
                  ?>
                  <input type="hidden" id="galry_date" name="galry_date" value="<?php echo $date; ?>">
                  <input type="hidden" id="cret_nonce" name="cret_nonce" value="<?php echo $once; ?>">
                  <input class="btn btn-primary" id="set_submit" type="submit" name="submit" value="Save">
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </body>
    </html>
  <?php } ?>
  <?php
  add_action( 'admin_footer', 'rpgs_set_action' ); // Write our JS below here
  function rpgs_set_action() { ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
      jQuery('#set_gallery').on('change', function () {
        var data = {
          'action': 'rpgs_action_settings',
          'set_id': jQuery('#set_gallery').val(),
          'galry_date': jQuery('#galry_date').val(),
          'cret_nonce': jQuery('#cret_nonce').val(),
        };
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function(response) {
          //	alert('Got this from the server: ' + response);
          var obj=JSON.parse(response);
          if(obj.status){
            var result=obj.records;
            console.log(result[0]);
            jQuery('[name="album_img_width"]').val(result[0].set_album_img_width);
            jQuery('[name="album_img_height"]').val(result[0].set_album_img_height);
            jQuery('[name="album_per_page"]').val(result[0].set_album_per_page);
            jQuery('[name="album_colmn"]').val(result[0].set_album_colmn);
            jQuery('[name="img_width"]').val(result[0].set_img_width);
            jQuery('[name="img_height"]').val(result[0].set_img_height);
            jQuery('[name="img_per_page"]').val(result[0].set_img_per_page);
            jQuery('[name="img_column"]').val(result[0].set_img_column);
            console.log(result[0].set_img_slider);
            if(result[0].set_img_slider==1){
              jQuery('[name="set_img_slider"]').prop('checked',true);
            }
            else {
              jQuery('[name="set_img_slider"]').prop('checked',false);
            }
            if(result[0].set_img_count==1){
              jQuery('[name="album_img_count"]').prop('checked',true);
            }
            else {
              jQuery('[name="album_img_count"]').prop('checked',false);
            }
            console.log(result[0].set_albm_title);
            if(result[0].set_albm_title==1){
              jQuery('[name="album_title"]').prop('checked',true);
            }
            else {
              jQuery('[name="album_title"]').prop('checked',false);
            }
            if(result[0].set_albm_ajx_paginatn==1){
              jQuery('[name="ajx_pagination"]').prop('checked',true);
            }
            else {
              jQuery('[name="ajx_pagination"]').prop('checked',false);
            }
            if(result[0].set_img_ajx_paginatn ==1){
              jQuery('[name="img_pagnation"]').prop('checked',true);
            }
            else {
              jQuery('[name="img_pagnation"]').prop('checked',false);
            }
            jQuery('#setings').show();
          }
        });
      });
    });

    </script> <?php
  }

  add_action( 'wp_ajax_rpgs_action_settings', 'rpgs_action_settings' );
  function rpgs_action_settings() {
    global $wpdb; // this is how you get access to the database
    $id = intval($_POST['set_id']);
    $table_settings=$wpdb->prefix.'rio_settings';
    if(wp_verify_nonce($_POST['cret_nonce'],'rpgs-nonce'.$_POST['galry_date'])){
      $set=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_settings WHERE gallery_id=%d", $id));
      $result['status']=true;
      if(!empty($set)){
        $result['records']=$set;
      }else {
        $set=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_settings WHERE gallery_id=%d",0));
        $result['records']=$set;
      }
    }
    else {
      $result['status']=false;
      $result['message']='Invalid access';
    }
    echo json_encode($result);
    wp_die(); // this is required to terminate immediately and return a proper response
  }


  add_action( 'admin_footer', 'rpgs_submit_action' ); // Write our JS below here
  function rpgs_submit_action() { ?>
    <script type="text/javascript" >
    jQuery(document).ready(function($) {
      jQuery('#settingsform').on('submit', function (e) {
        e.preventDefault();
        jQuery('#settingsform').serialize();
        var data = jQuery('#settingsform').serializeArray();
        data.push({name: 'action', value:'rpgs_save_settings'});
        //   var data = {
        //   'action': 'my_action_submit',
        //   'set_id': jQuery('#set_gallery').val(),
        // };
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function(response) {
          //	alert('Got this from the server: ' + response);
          var obj=JSON.parse(response);
          if(obj.status){

            $.notify({
              // options
              message: 'Updated'
            },{
              // settings
              type: 'success',
              delay: 300,
              animate: {
                enter: 'fadeInDown',
                exit: 'fadeOutUp'
              }
            });

            //  alert('ture');

          } else {
            //alert('false');
            $.notify({
              // options
              message: 'Faild to update'
            },{
              // settings
              type: 'danger',
              delay: 300,
              animate: {
                enter: 'fadeInDown',
                exit: 'fadeOutUp'
              }
            });
          }
        });
      });
    });
    </script>
    <?php
  }


  add_action( 'wp_ajax_rpgs_save_settings', 'rpgs_save_settings' );
  function rpgs_save_settings() {
    global $wpdb; // this is how you get access to the database
      if(!empty($_POST['albm_gallery'])) {
        if(wp_verify_nonce($_POST['cret_nonce'],'rpgs-nonce'.$_POST['galry_date'])){
      global $wpdb;
      $table_settings=$wpdb->prefix.'rio_settings';
      if(empty($_POST['set_img_slider'])){
        $_POST['set_img_slider']=0;
      }
      if(empty($_POST['set_img_count'])){
        $_POST['set_img_count']=0;
      }
      if(empty($_POST['set_img_count'])){
        $_POST['set_img_count']=0;
      }
      if(empty($_POST['set_albm_title'])){
        $_POST['set_albm_title']=0;
      }
      $settings=array(
        'gallery_id'=>intval($_POST['albm_gallery']),
        'set_album_img_width'=>intval($_POST['album_img_width']),
        'set_album_img_height'=>intval($_POST['album_img_height']),
        'set_album_per_page'=>intval($_POST['album_per_page']),
        'set_album_colmn'=>intval($_POST['album_colmn']),
        'set_img_count'=>intval($_POST['album_img_count']),
        'set_albm_title'=>intval($_POST['album_title']),
        'set_albm_ajx_paginatn' =>intval($_POST['ajx_pagination']),
        'set_img_width' =>intval($_POST['img_width']),
        'set_img_height' =>intval($_POST['img_height']),
        'set_img_per_page'=>intval($_POST['img_per_page']),
        'set_img_column' =>intval($_POST['img_column']),
        'set_img_ajx_paginatn' => intval($_POST['img_pagnation']),
        'set_img_slider' => intval($_POST['set_img_slider']),
        'created_at' =>date('Y:m:d H:i:s')
      );

      $matchThese=['gallery_id'=>intval($_POST['albm_gallery'])];
      $status=$wpdb->update($table_settings,$settings,$matchThese);
      if($status==0){
        $status= $wpdb->insert($table_settings,$settings);
      }
      if($status){
        $result['status']=true;
      }else {
        $result['status']=false;
      }
    } else {
      $result['status']=false;
    }
}
    else{
      $result['status']=false;
    }
    echo json_encode($result);
    wp_die(); // this is required to terminate immediately and return a proper response
  }

  ?>
