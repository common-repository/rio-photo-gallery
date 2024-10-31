<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
function rio_rpgs_albums() {
  ?>
  <?php
  /* start create album by under the gallery */
  if(isset($_POST["submit"]) && !empty ($_POST['album_name']) && !empty($_POST['gallery']) &&
  !empty($_FILES['album_image']['name'])) {
    if(wp_verify_nonce($_POST['cret_nonce'],'rpgs-nonce'.$_POST['galry_date'])){
      $extension = pathinfo(sanitize_file_name($_FILES['album_image']['name']), PATHINFO_EXTENSION);

      if(in_array($extension,['jpg','jpeg','gif','png','bmp']))
      {

        $path=WP_CONTENT_DIR.'/rio_uploads';
        $filename=Date('F Y');
        if(!file_exists($path.'/'.$filename)) {
           wp_mkdir_p($path.'/'.$filename);
        }
        $target=$path.'/'.$filename.'/';
        $type=explode('.',sanitize_file_name($_FILES['album_image']['name']));
        $type=end($type);
        $newname='Album_thumb_'.Date('dmyHis').microtime();
        $newname = str_replace(".", "", $newname);
        move_uploaded_file(sanitize_text_field($_FILES['album_image']['tmp_name']),$target.$newname.'.'.$type) ;
        global $wpdb;
        $tablealbum=$wpdb->prefix.'rio_album';
        $tableimage=$wpdb->prefix.'rio_image';
        $string = preg_replace('/\s+/', '-', sanitize_text_field($_POST[ 'album_name']));
        $data=array(
          'gallery_id' => intval($_POST['gallery']),
          'album_name' => sanitize_text_field($_POST[ 'album_name']),
          'album_slug' => sanitize_text_field(strtolower($string)),
          'album_thumb'=>$filename.'/'.$newname.'.'.$type,
          'created_at'=>date('Y-m-d H:i:s')
        );
        $status=$wpdb->insert( $tablealbum, $data);
        if($status){
          echo '<div class="alert alert-success" style="width: 50%; float:right"> Successfully Created </div>';

        } else{
          echo '<div class="alert alert-danger" style="width: 50%; float:right">Creation Failed </div>';
        }
        // get last id
        $lastid = $wpdb->insert_id;
        $countfiles = intval(count($_FILES['thumbimage']['name']));
        for($i=0;$i<$countfiles;$i++){

          if(!empty($_FILES['thumbimage']['name'][$i])) {
            $img_extension = pathinfo(sanitize_file_name($_FILES['thumbimage']['name'][$i]), PATHINFO_EXTENSION);
            if(in_array($img_extension,['jpg','jpeg','gif','png','bmp']))
            {
              $img_type=explode('.',sanitize_file_name($_FILES['thumbimage']['name'][$i]));
              $img_type=end($img_type);
              $newthumb='Album_thumb_img_'.Date('dmyHis').microtime();
              $newthumb=str_ireplace(".","",$newthumb);
              // Upload file
              move_uploaded_file(sanitize_text_field($_FILES['thumbimage']['tmp_name'][$i]),$target.$newthumb.'.'.$img_type);
              $imgs=array(
                'gallery_id' => intval($_POST['gallery']),
                'album_id' => intval($lastid),
                'image_path' => $filename.'/'.$newthumb.'.'.$img_type,
                'created_at' =>date('Y-m-d H:i:s')
              );
              $result=$wpdb->insert($tableimage,$imgs);
            }
          }
        }
      }
      else {
        echo '<div class="alert alert-danger" style="width: 50%; float:right">Invalid file type </div>';
      }
    } else {
      echo '<div class="alert alert-danger" style="width: 50%; float:right">Invalid access </div>';
    }
  }
  ?>
  <h2 class="wp-heading-inline mntitle1"> Create Albums </h2>
  <div class="clearfix"></div>
  <div class="m-md-top">
    <div class="col-md-7 padding-0">
      <form action="" method="POST" class="form-horizontal row" enctype="multipart/form-data" >
        <?php
        $date=Date('Y-m-d H:i:s');
        $once= wp_create_nonce('rpgs-nonce'.$date);
        ?>
        <div class="form-group">
          <label class="col-sm-4" for="formGroupExampleInput">New Album</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="album_name" name="album_name" placeholder="Enter Album Name" required/>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-4" for="formGroupExampleInput">Select Gallery</label>
          <div class="col-sm-8">
            <select class="form-control" name="gallery" required>
              <option value=""> Select Gallery</option>
              <?php
              global $wpdb;
              $table_gallery=$wpdb->prefix.'rio_gallery';
              $query =$wpdb->prepare("SELECT * FROM $table_gallery WHERE %d >= '0'", RID);
              $galleries = $wpdb->get_results($query);
              foreach ( $galleries as $gallery ) {
                ?>
                <option value="<?php echo $gallery->gallery_id; ?>"><?php echo stripslashes($gallery->gallery_name); ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-4" for="formGroupExampleInput"> Album Preview Image</label>
          <div class="col-sm-8">
            <input type="file" class="form-control" id="album_image" name="album_image" required/>
          </div>
        </div>
        <div class="fallback col-sm-8 pull-right m-md-btm">
          <label class="btn btn-info btn-sm" for="image">Upload Images</label>
          <i class="previewFileName" id="riopreview"></i>
          <input type="file" name="thumbimage[]" id="image" multiple="" style="display:none;">
        </div>
        <div class="al-right">
          <div class="col-md-12" id="resultdiv">
          </div>
        </div>
        <input type="hidden" name="galry_date" value="<?php echo $date; ?>">
        <input type="hidden" name="cret_nonce" value="<?php echo $once; ?>">
        <div class="form-group">
          <div class="col-sm-12 text-left">
            <input type="submit" class="btn-sm btn btn-primary"  value="Add Album" name="submit">
            <a href="<?php echo admin_url('admin.php?page=manage-albums'); ?>" class="btn btn-success btn-sm">View Albums </a>
          </div>
        </div>
      </form>
      <!-- end create album by under the gallery  -->
    </div>
    <div class="col-sm-6 padding-0 pull-right">
    </div>
  </div>
  <script type="text/javascript">
  jQuery(".alert-success").fadeTo(1500, 400).slideUp(400, function(){
    jQuery(".alert-success").slideUp(400);
  });
  </script>
  <script type="text/javascript">
  jQuery(".alert-danger").fadeTo(1500, 400).slideUp(400, function(){
    jQuery(".alert-danger").slideUp(400);
  });
  </script>

  <script>
  jQuery(document).on('change','#image',function(){
    if(jQuery('#image').val()!='')
    {
      var length=jQuery('#image')[0].files.length;
      for(var i=0;i<length;i++)
      {
        var FR=new FileReader();
        FR.readAsDataURL(jQuery('#image')[0].files[i]);
        FR.addEventListener("load",function(e){
          var imginput='<div class="col-md-4 imagelist" id="div'+((jQuery('.imagelist').length)+1)+'" ><div class="hide-button"><a href="javascript:;" class="closeimage" role="'+((jQuery('.imagelist').length)+1)+'">x</a></div><img id="image'+((jQuery('.imagelist').length)+1)+'" src="'+e.target.result+'"><input id="value'+((jQuery('.imagelist').length)+1)+'" name="'+((jQuery('.imagelist').length)+1)+'" type="hidden" value="'+e.target.result+'"></div>';
          jQuery('#resultdiv').html(jQuery('#resultdiv').html()+imginput);
        })
      }
      currentlength+=length;
      jQuery('#filecount').val(currentlength);
    }
  })

  jQuery(document).delegate('.closeimage','click',function(){
    var id=jQuery(this).attr('role');
    jQuery('#image'+id).remove();
    jQuery('#value'+id).remove();
    jQuery('#div'+id).remove();
  });
  </script>
<?php }

/* manage albums */
function rio_rpgs_manage_albums(){

  ?>
  <h2 class="wp-heading-inline mntitle1"> Manage Albums</h2>
  <?php
  /* start delete albums by bulk or selected */
  if(isset($_POST['submit'])) {
    if(wp_verify_nonce($_POST['cret_nonce'],'rpgs-nonce'.$_POST['galry_date'])){
      if(!empty($_POST['selected_all']) && $_POST['selected_all']=='true') {
        global $wpdb;
        $tablename=$wpdb->prefix.'rio_album';
        $table_images=$wpdb->prefix.'rio_image';
        $result=$wpdb->get_results("SELECT * FROM $tablename");
        foreach ($result as $value){
          $img_file=WP_CONTENT_DIR.'/rio_uploads/'.$value->album_thumb;
          if(!empty($img_file)){
            unlink($img_file);
          } }
          $delete = $wpdb->query("TRUNCATE TABLE $tablename");
          $result_imgs=$wpdb->get_results("SELECT * FROM $table_images ");
          foreach ($result_imgs as $imgs ) {
            $imge_files=WP_CONTENT_DIR.'/rio_uploads/'.$imgs->image_path;
            if(!empty($imge_files)) {
              unlink($imge_files);
            } }
            $delete_image = $wpdb->query("TRUNCATE TABLE $table_images");
          } else {
            if(!empty($_POST['selected'])) {
              $values=sanitize_text_field($_POST['selected']);
              $value=rtrim($values,',');
              global $wpdb;
              $tablename=$wpdb->prefix.'rio_album';
              $table_images=$wpdb->prefix.'rio_image';
              $result=$wpdb->get_results("SELECT * FROM $tablename WHERE album_id IN($value)");
              foreach ($result as $results) {
                $img_file=WP_CONTENT_DIR.'/rio_uploads/'.$results->album_thumb;
                if(!empty($img_file)) {
                  unlink($img_file);
                } }
                $response=$wpdb->query( "DELETE FROM $tablename WHERE album_id IN($value)" );
                $img_results=$wpdb->get_results("SELECT * FROM $table_images WHERE album_id IN($value)");
                foreach ($img_results as $img_result) {
                  $img_files=WP_CONTENT_DIR.'/rio_uploads/'.$img_result->image_path;
                  if(!empty($img_files)) {
                    unlink($img_files);
                  } }
                  $rese_img=$wpdb->query("DELETE FROM $table_images WHERE album_id IN($value)");
                }
              }
            } else {
              echo '<div class="alert alert-danger" style="width: 40%; float:right">Invalid Access </div>';
            }
          }
          /* end delete albums by bulk or selected */
          ?>
          <div class="btns-add">
          </div>
          <div class="wrap">
            <form class="" action="" method="post">
              <?php
              $date=Date('Y-m-d H:i:s');
              $once= wp_create_nonce('rpgs-nonce'.$date);
              ?>
              <a href="<?php echo admin_url('admin.php?page=add-albums'); ?>" class="page-title-action"> Add Albums </a>
              <input type="submit" name="submit" value="Delete" class="btn-sm btn btn-delete page-title-action ">
              <input type="hidden" id="selected" name="selected" value="">
              <input type="hidden" id="selected_all" name="selected_all" value="false">
              <input type="hidden" name="galry_date" value="<?php echo $date; ?>">
              <input type="hidden" name="cret_nonce" value="<?php echo $once; ?>">
              <table id="example" class="wp-list-table widefat fixed striped posts" style="width:100%" >
                <thead>
                  <tr>
                    <th style="width:35px"> <input type="checkbox" name="" value="" id="check"> </th>
                    <th style="width:70px">Slno.</th>
                    <th>Album Title</th>
                    <th>Gallery</th>
                    <th>Images</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i=1;
                  global $wpdb;
                  $table_album=$wpdb->prefix.'rio_album';
                  $table_images=$wpdb->prefix.'rio_image';
                  $table_gallery=$wpdb->prefix.'rio_gallery';
                  $query=$wpdb->prepare("SELECT *,(SELECT gallery_name FROM $table_gallery WHERE $table_gallery.gallery_id=$table_album.gallery_id) as galleryname ,(SELECT Count(album_id) FROM $table_images WHERE $table_images.album_id=$table_album.album_id) as albumcount FROM $table_album WHERE %d >= '0'", RID);
                  $albums=$wpdb->get_results($query);
                  $date=Date('Y-m-d H:i:s');
                  $once= wp_create_nonce('rpgs-nonce'.$date);
                  foreach ( $albums as $album ) {
                    ?>
                    <tr>
                      <td><input id="<?php echo $album->album_id; ?>" type="checkbox" value="<?php echo $album->album_id;?>" data-item="chekbox"></td>
                      <td><?php echo $i; ?></td>
                      <td><?php echo stripslashes($album->album_name); ?></td>
                      <td><?php echo stripslashes($album->galleryname); ?></td>
                      <td><?php echo $album->albumcount; ?></td>
                      <td> <a href="<?php echo admin_url('admin.php?page=edit-album&id='.$album->album_id); ?>" id="<?php echo $album->album_id; ?>" class="btn-xs btn btn-info"> Edit </a>
                        <button type="button" class="btn-xs btn btn-danger albumdlt" name="button" id="<?php echo $album->album_id; ?>"> Delete</button>
                        <input type="hidden" id="galry_date" name="galry_date" value="<?php echo $date; ?>">
                        <input type="hidden" id="cret_nonce" name="cret_nonce" value="<?php echo $once; ?>">
                      </td>
                    </tr>
                    <?php  $i++; } ?>
                  </tbody>
                </table>
              </form>
            </div>

            <script>
            jQuery(document).ready(function(){
              jQuery("#check").click(function(){
                var flag=jQuery("#check").prop('checked');
                jQuery('input[data-item="chekbox"]').prop('checked',flag);
                jQuery('#selected_all').val(flag);
              });
            });
            jQuery(document).on('click','input[data-item="chekbox"]',function(){
              if(this.checked){
                var selected=jQuery('#selected').val();
                var id=jQuery(this).val();
                jQuery('#selected').val(selected+id+',');
              }else {
                var selected=jQuery('#selected').val();
                var id=jQuery(this).val();
                selected=selected.replace(id+',','');
                jQuery('#selected').val(selected);
              }
            })
            </script>
            <script type="text/javascript">
            jQuery('#example').dataTable( {
              "columnDefs": [
                { "sortable": false, "targets": [0,4,5] }
              ],
              "order": [[ 1, "asc" ]]
            } );
            </script>
            <script type="text/javascript">
            jQuery(document).ready(function() {
              jQuery('.example').DataTable();
            } );
            </script>

            <?php
          }

          /* Edit album */
          function rio_rpgs_edit_albums() { ?>
            <?php
            global $wpdb;
            $table_album=$wpdb->prefix.'rio_album';
            $tableimage=$wpdb->prefix.'rio_image';
            if(isset($_POST['submit']) && !empty($_POST['album_name']) &&
            !empty($_POST['album_gallery']) && !empty($_FILES['thumbimage']['name'])) {
              if(wp_verify_nonce($_POST['cret_nonce'],'rpgs-nonce'.$_POST['galry_date'])){


                $path=WP_CONTENT_DIR.'/rio_uploads';
                $filename=Date('F Y');
                if(!file_exists($path.'/'.$filename)) {
                  wp_mkdir_p($path.'/'.$filename);
                }
                $target=$path.'/'.$filename.'/';
                if(!empty($_FILES['album_image']['name'])){
                  $type=explode('.',sanitize_file_name($_FILES['album_image']['name']));
                  $type=end($type);
                  $newname='Album_thumb_'.Date('dmyHis').microtime();
                  $newname = str_replace(".", "", $newname);
                  move_uploaded_file(sanitize_text_field($_FILES['album_image']['tmp_name']),$target.$newname.'.'.$type) ;
                  $path=$filename.'/'.$newname.'.'.$type;
                }else {
                  $path=sanitize_text_field($_POST['old_path']);
                }
                $editextension = pathinfo($path, PATHINFO_EXTENSION);
                if(in_array($editextension,['jpg','jpeg','gif','png','bmp'])) {
                  $string = preg_replace('/\s+/', '-',sanitize_text_field($_POST['album_name']));
                  $data=array(
                    'album_name' => sanitize_text_field($_POST['album_name']),
                    'album_slug' => sanitize_text_field(strtolower($string)),
                    'gallery_id' => intval($_POST['album_gallery']),
                    'album_thumb'=> $path,
                    'created_at'=>date('Y-m-d H:i:s')
                  );

                  $matchThese=['album_id'=>intval($_GET['id'])];
                  $update=$wpdb->update($table_album,$data,$matchThese);
                  if($update){
                    echo '<div class="alert alert-success" style="width: 50%; float:right"> Successfully Updated </div>';
                  } else {
                    echo '<div class="alert alert-danger" style="width: 50%; float:right">Faild to update</div>';
                  } } else {
                    echo '<div class="alert alert-danger" style="width: 50%; float:right">Invalid File type</div>';
                  }

                  $countfiles = intval(count($_FILES['thumbimage']['name']));
                  $lastid = $wpdb->insert_id;
                  for ($i=0; $i< $countfiles; $i++) {
                    if(!empty($_FILES['thumbimage']['name'][$i])) {
                      $editimgextension = pathinfo(sanitize_file_name($_FILES['thumbimage']['name'][$i]), PATHINFO_EXTENSION);
                      if(in_array($editimgextension,['jpg','jpeg','gif','png','bmp'])) {
                        $typenew=explode('.',sanitize_file_name($_FILES['thumbimage']['name'][$i]));
                        $typenew=end($typenew);
                        $newthumb='Album_thumb_img'.Date('dmyHis').microtime();
                        $newthumb = str_replace(".", "", $newthumb);
                        move_uploaded_file(sanitize_text_field($_FILES['thumbimage']['tmp_name'][$i]),$target.$newthumb.'.'.$typenew) ;
                        $imgs=array(
                          'gallery_id' => intval($_POST['album_gallery']),
                          'album_id' =>intval($_GET['id']),
                          'image_path' => $filename.'/'.$newthumb.'.'.$typenew,
                          'created_at' =>date('Y-m-d H:i:s')
                        );
                        $wpdb->insert($tableimage,$imgs);
                      } }
                    }
                  } else {
                    echo '<div class="alert alert-danger" style="width: 40%; float:right">Invalid Access </div>';
                  }
                }
                $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_album WHERE album_id=%d", intval($_GET['id'])));
                ?>
                <h3> Edit Album </h3>
                <div class="wrap">
                  <div class="row">
                    <div class="col-md-4 m-md-top">
                      <form action=""  method="post" enctype="multipart/form-data">
                        <?php
                        $date=Date('Y-m-d H:i:s');
                        $once= wp_create_nonce('rpgs-nonce'.$date);
                        ?>
                        <div class="form-group">
                          <label for="formGroupExampleInput">Album Name</label>
                          <input type="text" class="form-control" id="album_name" name="album_name" value="<?php echo stripslashes($results[0]->album_name); ?>">
                        </div>
                        <div class="form-group">
                          <label for="formGroupExampleInput">Gallery</label>
                          <select class="form-control" name="album_gallery">
                            <?php
                            global $wpdb;
                            $table_gallery=$wpdb->prefix.'rio_gallery';
                            $query =$wpdb->prepare("SELECT * FROM $table_gallery",[]);
                            $galleries = $wpdb->get_results($query);
                            foreach ( $galleries as $gallery ) {
                              if($results[0]->gallery_id==$gallery->gallery_id){
                                ?>
                                <option value="<?php echo $gallery->gallery_id; ?>" selected><?php echo stripslashes($gallery->gallery_name); ?></option>
                                <?php
                              }
                              else {
                                ?>
                                <option value="<?php echo $gallery->gallery_id; ?>"><?php echo stripslashes($gallery->gallery_name); ?></option>
                                <?php
                              }
                              ?>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="form-group">
                          <label for="formGroupExampleInput">Album Preview Image</label>
                          <input type="file" class="form-control" id="album_image" name="album_image">
                          <div class="album_main">
                            <img src="<?php echo content_url().'/rio_uploads/'. $results[0]->album_thumb ; ?>" alt="" width="120" height="">
                            <input type="hidden" name="old_path" value="<?php echo $results[0]->album_thumb;?>">
                          </div>
                        </div>
                        <div class="form-group">
                          <div class="fallback">
                            <label class="uploadButton m-md-btm z_upload_image_button button" for="image">Upload Images</label>
                            <i class="previewFileName" id="riopreview"></i>
                            <input name="thumbimage[]" id="image" type="file" multiple style="display:none;" />
                          </div>
                          <div class="al-right">
                            <div class="col-md-12" id="resultdiv">
                            </div>
                          </div>
                        </div>
                        <input type="hidden" name="galry_date" value="<?php echo $date; ?>">
                        <input type="hidden" name="cret_nonce" value="<?php echo $once; ?>">

                        <div class="form-group">
                          <input type="submit" class="btn-sm btn btn-primary" name="submit" value="Update">
                        </div>
                      </form>
                    </div>
                    <?php
                    if(isset($_POST['submit'])) {
                      if(wp_verify_nonce($_POST['cret_nonce'],'rpgs-nonce'.$_POST['galry_date'])){
                        if(!empty($_POST['selected_all']) && $_POST['selected_all']=='true') {
                          global $wpdb;
                          $tablename=$wpdb->prefix.'rio_image';
                          $result=$wpdb->get_results( $wpdb->prepare("SELECT * FROM $tablename WHERE album_id= %d", intval($_GET['id'])));
                          foreach ($result as $results) {
                            $all_files=WP_CONTENT_DIR.'/rio_uploads/'.$results->image_path;
                            if(!empty($all_files)){
                              unlink($all_files);
                            } }

                            $delete=$wpdb->query($wpdb->prepare( "DELETE FROM $tablename WHERE album_id= %d", intval($_GET['id'])));
                          } else {
                            if(!empty($_POST['selected'])) {
                              $values=sanitize_text_field($_POST['selected']);
                              $value=rtrim($values,',');
                              global $wpdb;
                              $tablename=$wpdb->prefix.'rio_image';
                              $results=$wpdb->get_results("SELECT * FROM $tablename where image_id IN ($value)");
                              foreach ($results as $result) {
                                $img_files=WP_CONTENT_DIR.'/rio_uploads/'.$result->image_path;
                                if(!empty($img_files)) {
                                  unlink($img_files);
                                }
                              }
                              $response=$wpdb->query( "DELETE FROM $tablename WHERE image_id IN($value)" );
                            }
                          }
                        } else {
                          echo '<div class="alert alert-danger" style="width: 40%; float:right">Invalid Access </div>';
                        }
                      }
                      ?>
                      <div class="col-sm-8">
                        <form class="" action="" method="post">
                          <?php
                          $date=Date('Y-m-d H:i:s');
                          $once= wp_create_nonce('rpgs-nonce'.$date);
                          ?>
                          <input type="submit" name="submit" class="btn-sm btn btn-top btn-danger" value="Delete">
                          <input type="hidden" id="selected" name="selected" value="">
                          <input type="hidden" id="selected_all" name="selected_all" value="false">
                          <input type="hidden" name="galry_date" value="<?php echo $date; ?>">
                          <input type="hidden" name="cret_nonce" value="<?php echo $once; ?>">
                          <table id="example" class="wp-list-table widefat fixed striped posts dataTable no-footer" style="width:100%">
                            <thead>
                              <tr>
                                <th style="width:35px"> <input type="checkbox" name="" value="" id="check"> </th>
                                <th style="width: 68px;">Slno.</th>
                                <th>Thumbnail</th>
                                <th>Delete</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              global $wpdb;
                              $i=1;

                              $table_image=$wpdb->prefix.'rio_image';
                              $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_image WHERE album_id= %d", intval($_GET['id'])) ) ;
                              foreach ( $results as $result ) {
                                ?>
                                <tr>
                                  <td><input id="<?php echo $result->image_id; ?>" type="checkbox" name="" value="<?php echo $result->image_id ; ?>" data-item="chekbox"></td>
                                  <td> <?php echo $i; ?></td>
                                  <td><img src="<?php echo content_url().'/rio_uploads/'. $result->image_path ; ?>" alt="" width="40" height="40"></td>
                                  <td><button type="button" class="btn-sm btn btn-danger imgdlt" name="button" id="<?php echo $result->image_id; ?>"> Delete</button>
                                    <input type="hidden" id="galry_date" name="galry_date" value="<?php echo $date; ?>">
                                    <input type="hidden" id="cret_nonce" name="cret_nonce" value="<?php echo $once; ?>">
                                  </td>
                                </tr>
                                <?php $i++; } ?>
                              </tbody>
                            </table>
                          </form>
                        </div>
                      </div>
                    </div>

                    <script>
                    jQuery(document).ready(function(){
                      jQuery("#check").click(function(){
                        var flag=jQuery("#check").prop('checked');
                        jQuery('input[data-item="chekbox"]').prop('checked',flag);
                        jQuery('#selected_all').val(flag);
                      });
                    });
                    jQuery(document).on('click','input[data-item="chekbox"]',function(){
                      if(this.checked){
                        var selected=jQuery('#selected').val();
                        var id=jQuery(this).val();
                        jQuery('#selected').val(selected+id+',');
                      }else {
                        var selected=jQuery('#selected').val();
                        var id=jQuery(this).val();
                        selected=selected.replace(id+',','');
                        jQuery('#selected').val(selected);
                      }
                    })
                    </script>
                    <script type="text/javascript">
                    jQuery(".alert-success").fadeTo(1500, 400).slideUp(400, function(){
                      jQuery(".alert-success").slideUp(400);
                    });
                    </script>
                    <script type="text/javascript">
                    jQuery(".alert-danger").fadeTo(1500, 400).slideUp(400, function(){
                      jQuery(".alert-danger").slideUp(400);
                    });
                    </script>

                    <script type="text/javascript">
                    jQuery('#example').dataTable( {
                      "columnDefs": [
                        { "sortable": false, "targets": [0,3] }
                      ],
                      "order": [[ 1, "asc" ]],
                      "searching": false
                    } );
                    </script>

                    <script type="text/javascript">
                    jQuery(document).ready(function() {
                      jQuery('.example').DataTable();
                    } );
                    </script>


                    <script>
                    jQuery(document).on('change','#image',function(){
                      if(jQuery('#image').val()!='')
                      {
                        var length=jQuery('#image')[0].files.length;
                        for(var i=0;i<length;i++)
                        {
                          var FR=new FileReader();
                          FR.readAsDataURL(jQuery('#image')[0].files[i]);
                          FR.addEventListener("load",function(e){
                            var imginput='<div class="col-md-4 imagelist" id="div'+((jQuery('.imagelist').length)+1)+'" ><div class="hide-button"><a href="javascript:;" class="closeimage" role="'+((jQuery('.imagelist').length)+1)+'">x</a></div><img id="image'+((jQuery('.imagelist').length)+1)+'" src="'+e.target.result+'"><input id="value'+((jQuery('.imagelist').length)+1)+'" name="'+((jQuery('.imagelist').length)+1)+'" type="hidden" value="'+e.target.result+'"></div>';
                            jQuery('#resultdiv').html(jQuery('#resultdiv').html()+imginput);
                          })
                        }
                        currentlength+=length;
                        jQuery('#filecount').val(currentlength);
                      }
                    })

                    jQuery(document).delegate('.closeimage','click',function(){
                      var id=jQuery(this).attr('role');
                      jQuery('#image'+id).remove();
                      jQuery('#value'+id).remove();
                      jQuery('#div'+id).remove();
                    });
                    </script>
                  <?php }

                  function rio_rpgs_individual_albums() {
                    ?>
                    <div class="wrap">
                      <h3 class="wp-heading-inline mntitle1 pull-left">Albums</h3>
                      <?php
                      if(isset($_POST['submit'])) {
                        if(wp_verify_nonce($_POST['cret_nonce'],'rpgs-nonce'.$_POST['galry_date'])){
                          if(!empty($_POST['selected_all']) && $_POST['selected_all']=='true') {
                            global $wpdb;
                            $tablename=$wpdb->prefix.'rio_album';
                            $table_images=$wpdb->prefix.'rio_image';
                            $result=$wpdb->get_results($wpdb->prepare("SELECT * FROM $tablename WHERE gallery_id = %d",intval($_GET['id'])));
                            foreach ($result as $value){
                              $img_file=WP_CONTENT_DIR.'/rio_uploads/'.$value->album_thumb;
                              if(!empty($img_file)){
                                unlink($img_file);
                              } }

                              $delete=$wpdb->query("DELETE FROM $tablename WHERE gallery_id=".intval($_GET['id']));
                              $result_imgs=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_images gallery_id= %d" ,intval($_GET['id'])));
                              foreach ($result_imgs as $imgs ) {
                                $imge_files=WP_CONTENT_DIR.'/rio_uploads/'.$imgs->image_path;
                                if(!empty($imge_files)) {
                                  unlink($imge_files);
                                } }

                                $delete_image=$wpdb->query("DELETE FROM $table_images WHERE album_id=".intval($_GET['id']));
                              } else {
                                if(!empty($_POST['selected'])) {
                                  $values=sanitize_text_field($_POST['selected']);
                                  $value=rtrim($values,',');
                                  global $wpdb;
                                  $tablename=$wpdb->prefix.'rio_album';
                                  $table_images=$wpdb->prefix.'rio_image';
                                  $result=$wpdb->get_results("SELECT * FROM $tablename WHERE album_id IN($value)");
                                  foreach ($result as $results) {
                                    $img_file=WP_CONTENT_DIR.'/rio_uploads/'.$results->album_thumb;
                                    if(!empty($img_file)) {
                                      unlink($img_file);
                                    } }
                                    $response=$wpdb->query( "DELETE FROM $tablename WHERE album_id IN($value)" );
                                    $img_results=$wpdb->get_results("SELECT * FROM $table_images WHERE album_id IN($value)");
                                    foreach ($img_results as $img_result) {
                                      $img_files=WP_CONTENT_DIR.'/rio_uploads/'.$img_result->image_path;
                                      if(!empty($img_files)) {
                                        unlink($img_files);
                                      } }
                                      $rese_img=$wpdb->query("DELETE FROM $table_images WHERE album_id IN($value)");
                                    }
                                  }
                                } else {
                                  echo '<div class="alert alert-danger" style="width: 40%; float:right">Invalid Access </div>';
                                }
                              }
                              ?>
                              <form class="" action="" method="post">
                                <?php
                                $date=Date('Y-m-d H:i:s');
                                $once= wp_create_nonce('rpgs-nonce'.$date);
                                ?>
                                <a href="<?php echo admin_url('admin.php?page=individual-create-albums&id='.intval($_GET['id'])); ?>" class="page-title-action"> Add Albums </a>
                                <input type="submit" name="submit" value="Delete" class="btn-sm btn page-title-action">
                                <input type="hidden" id="selected" name="selected" value="">
                                <input type="hidden" id="selected_all" name="selected_all" value="false">
                                <input type="hidden" name="galry_date" value="<?php echo $date; ?>">
                                <input type="hidden" name="cret_nonce" value="<?php echo $once; ?>">
                                <table id="example" class="wp-list-table widefat fixed striped posts" style="width:100%" >
                                  <thead>
                                    <tr>
                                      <th style="width:35px"> <input type="checkbox" name="" value="" id="check"> </th>
                                      <th>Slno.</th>
                                      <th>Album Title</th>
                                      <th>Images</th>
                                      <th>Actions</th>

                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php
                                    $i=1;
                                    global $wpdb;
                                    $table_album=$wpdb->prefix.'rio_album';
                                    $table_images=$wpdb->prefix.'rio_image';
                                    $query =$wpdb->prepare("SELECT *,(SELECT Count(album_id) FROM $table_images WHERE $table_images.album_id=$table_album.album_id) as albumcount FROM $table_album WHERE gallery_id=".intval($_GET['id']), '');
                                    $albums = $wpdb->get_results($query);

                                    foreach ( $albums as $album ) {
                                      ?>
                                      <tr>
                                        <td><input id="<?php echo $album->album_id; ?>" type="checkbox" value="<?php echo $album->album_id;?>" data-item="chekbox"></td>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $album->album_name; ?></td>
                                        <td><?php echo $album->albumcount; ?></td>
                                        <td> <a href="<?php echo admin_url('admin.php?page=edit-album&id='.$album->album_id); ?>" id="<?php echo $album->album_id; ?>" class="btn-xs btn btn-info"> Edit </a>
                                          <button type="button" class="btn-xs btn btn-danger albumdlt" name="button" id="<?php echo $album->album_id; ?>"> Delete</button>
                                          <input type="hidden" id="galry_date" name="galry_date" value="<?php echo $date; ?>">
                                          <input type="hidden" id="cret_nonce" name="cret_nonce" value="<?php echo $once; ?>">
                                        </td>
                                      </tr>
                                      <?php  $i++; } ?>
                                    </tbody>
                                  </table>
                                </form>
                              </div>
                              <script>
                              jQuery(document).ready(function(){
                                jQuery("#check").click(function(){
                                  var flag=jQuery("#check").prop('checked');
                                  jQuery('input[data-item="chekbox"]').prop('checked',flag);
                                  jQuery('#selected_all').val(flag);
                                });
                              });
                              jQuery(document).on('click','input[data-item="chekbox"]',function(){
                                if(this.checked){
                                  var selected=jQuery('#selected').val();
                                  var id=jQuery(this).val();
                                  jQuery('#selected').val(selected+id+',');
                                }else {
                                  var selected=jQuery('#selected').val();
                                  var id=jQuery(this).val();
                                  selected=selected.replace(id+',','');
                                  jQuery('#selected').val(selected);
                                }
                              })
                              </script>
                              <script type="text/javascript">
                              jQuery('#example').dataTable( {
                                "columnDefs": [
                                  { "sortable": false, "targets": [0,4] }
                                ],
                                "order": [[ 1, "asc" ]]
                              } );
                              </script>
                              <script type="text/javascript">
                              jQuery(document).ready(function() {
                                jQuery('.example').DataTable();
                              } );
                              </script>
                            <?php }

                            function rio_rpgs_individual_create_albums() {
                              ?>
                              <?php
                              if(isset($_POST["submit"]) && !empty($_POST['album_name']) && !empty($_POST['gallery'])
                              && !empty($_FILES['album_image']['name'])) {
                                  if(wp_verify_nonce($_POST['cret_nonce'],'rpgs-nonce'.$_POST['galry_date'])){
                                $indiextension = pathinfo(sanitize_file_name($_FILES['album_image']['name']), PATHINFO_EXTENSION);

                                if(in_array($indiextension,['jpg','jpeg','gif','png','bmp'])){
                                  $path=WP_CONTENT_DIR.'/rio_uploads';
                                  $filename=Date('F Y');
                                  if(!file_exists($path.'/'.$filename)) {
                                    wp_mkdir_p($path.'/'.$filename);
                                  }
                                  $target=$path.'/'.$filename.'/';
                                  $type=explode('.',sanitize_file_name($_FILES['album_image']['name']));
                                  $type=end($type);
                                  $newname='Album_thumb_'.Date('dmyHis').microtime();
                                  $newname = str_replace(".", "", $newname);
                                  $status=move_uploaded_file(sanitize_text_field($_FILES['album_image']['tmp_name']),$target.$newname.'.'.$type) ;
                                  global $wpdb;
                                  $tablealbum=$wpdb->prefix.'rio_album';
                                  $tableimage=$wpdb->prefix.'rio_image';
                                  $string = preg_replace('/\s+/', '-', sanitize_text_field($_POST[ 'album_name']));
                                  $data=array(
                                    'gallery_id' => intval($_POST['gallery']),
                                    'album_name' =>sanitize_text_field($_POST[ 'album_name']),
                                    'album_slug' => sanitize_text_field(strtolower($string)),
                                    'album_thumb'=>$filename.'/'.$newname.'.'.$type,
                                    'created_at'=>date('Y-m-d H:i:s')
                                  );
                                  $status=$wpdb->insert( $tablealbum, $data);
                                  if($status){
                                    echo '<div class="alert alert-success" style="width:50%; float:right"> Successfully Created </div>';
                                  } else{
                                    echo '<div class="alert alert-danger" style="width: 50%; float:right">Creation Failed </div>';
                                  }
                                }else{
                                  echo '<div class="alert alert-danger" style="width: 50%; float:right">Invalid File Type </div>';
                                }
                                // get last id
                                $lastid = $wpdb->insert_id;
                                $countfiles = intval(count($_FILES['thumbimage']['name']));
                                for($i=0;$i<$countfiles;$i++){
                                  if(!empty($_FILES['thumbimage']['name'][$i])) {
                                    $indimgextension = pathinfo(sanitize_file_name($_FILES['thumbimage']['name'][$i]), PATHINFO_EXTENSION);
                                    if(in_array($indimgextension,['jpg','jpeg','gif','png','bmp'])) {
                                      $img_type=explode('.',sanitize_file_name($_FILES['thumbimage']['name'][$i]));
                                      $img_type=end($img_type);
                                      $newthumb='Album_thumb_img_'.Date('dmyHis').microtime();
                                      $newthumb=str_ireplace(".","",$newthumb);
                                      // Upload file
                                      move_uploaded_file(sanitize_text_field($_FILES['thumbimage']['tmp_name'][$i]),$target.$newthumb.'.'.$img_type);
                                      $imgs=array(
                                        'gallery_id' => intval($_POST['gallery']),
                                        'album_id' =>intval($lastid),
                                        'image_path' => $filename.'/'.$newthumb.'.'.$img_type,
                                        'created_at' =>date('Y-m-d H:i:s')
                                      );
                                      $result=$wpdb->insert($tableimage,$imgs);
                                    }  } }
                                  } else {
                                      echo '<div class="alert alert-danger" style="width: 40%; float:right">Invalid Access </div>';
                                  }
                                }
                                  ?>
                                  <h2 class="wp-heading-inline mntitle1"> Create Albums </h2>
                                  <div class="clearfix"></div>
                                  <div class="m-md-top">
                                    <div class="col-md-7 padding-0">
                                      <form action="" method="POST" class="form-horizontal row" enctype="multipart/form-data" >
                                        <?php
                                        $date=Date('Y-m-d H:i:s');
                                        $once= wp_create_nonce('rpgs-nonce'.$date);
                                        ?>
                                        <div class="form-group">
                                          <label class="col-sm-4" for="formGroupExampleInput">New Album</label>
                                          <div class="col-sm-8">
                                            <input type="text" class="form-control" id="album_name" name="album_name" placeholder="New Album" required/>
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="col-sm-4" for="formGroupExampleInput">Select Gallery</label>
                                          <div class="col-sm-8">
                                            <select class="form-control" name="gallery" required>
                                              <?php
                                              global $wpdb;
                                              $table_gallery=$wpdb->prefix.'rio_gallery';
                                              $query =$wpdb->prepare("SELECT * FROM $table_gallery WHERE %d >= '0'", RID);
                                              $galleries = $wpdb->get_results($query);
                                              foreach ( $galleries as $gallery ) {
                                                if($gallery->gallery_id==$_GET['id']) {
                                                  ?>
                                                  <option selected value="<?php echo $gallery->gallery_id; ?>"><?php echo stripslashes($gallery->gallery_name); ?></option>
                                                <?php } else { ?>
                                                  <option value="<?php echo $gallery->gallery_id; ?>"><?php echo stripslashes($gallery->gallery_name); ?></option>
                                                <?php } } ?>
                                              </select>
                                            </div>
                                          </div>
                                          <div class="form-group">
                                            <label class="col-sm-4" for="formGroupExampleInput"> Album Preview Image</label>
                                            <div class="col-sm-8">
                                              <input type="file" class="form-control" id="album_image" name="album_image" required/>
                                            </div>
                                          </div>
                                          <div class="fallback col-sm-8 pull-right m-md-btm">
                                            <label class="btn btn-info btn-sm" for="image">Upload Images</label>
                                            <i class="previewFileName" id="riopreview"></i>
                                            <input type="file" name="thumbimage[]" id="image" multiple="" style="display:none;">
                                          </div>
                                          <div class="al-right">
                                            <div class="col-md-12" id="resultdiv">
                                            </div>
                                          </div>
                                          <input type="hidden" name="galry_date" value="<?php echo $date; ?>">
                                          <input type="hidden" name="cret_nonce" value="<?php echo $once; ?>">
                                          <div class="form-group">
                                            <div class="col-sm-12 text-left">
                                              <input type="submit" class="btn-sm btn btn-primary"  value="Add Album" name="submit">
                                              <a href="<?php echo admin_url('admin.php?page=individual-albums&id='.$_GET['id']); ?>" class="btn btn-success btn-sm">View Albums </a>
                                            </div>
                                          </div>
                                        </form>
                                        <!-- end create album by under the gallery  -->
                                      </div>
                                      <div class="col-sm-6 padding-0 pull-right">
                                      </div>
                                    </div>


          <script type="text/javascript">
          jQuery(".alert-success").fadeTo(1500, 400).slideUp(400, function(){
            jQuery(".alert-success").slideUp(400);
          });
          </script>
          <script type="text/javascript">
          jQuery(".alert-danger").fadeTo(1500, 400).slideUp(400, function(){
            jQuery(".alert-danger").slideUp(400);
          });
          </script>

          <script>
          jQuery(document).on('change','#image',function(){
            if(jQuery('#image').val()!='')
            {
              var length=jQuery('#image')[0].files.length;
              for(var i=0;i<length;i++)
              {
                var FR=new FileReader();
                FR.readAsDataURL(jQuery('#image')[0].files[i]);
                FR.addEventListener("load",function(e){
                  var imginput='<div class="col-md-3 imagelist" id="div'+((jQuery('.imagelist').length)+1)+'" ><div class="hide-button"><a href="javascript:;" class="closeimage" role="'+((jQuery('.imagelist').length)+1)+'">x</a></div><img id="image'+((jQuery('.imagelist').length)+1)+'" src="'+e.target.result+'"><input id="value'+((jQuery('.imagelist').length)+1)+'" name="'+((jQuery('.imagelist').length)+1)+'" type="hidden" value="'+e.target.result+'"></div>';
                  jQuery('#resultdiv').html(jQuery('#resultdiv').html()+imginput);
                })
              }
              currentlength+=length;
              jQuery('#filecount').val(currentlength);
            }
          })

          jQuery(document).delegate('.closeimage','click',function(){
            var id=jQuery(this).attr('role');
            jQuery('#image'+id).remove();
            jQuery('#value'+id).remove();
            jQuery('#div'+id).remove();
          });
          </script>

        <?php }  ?>
