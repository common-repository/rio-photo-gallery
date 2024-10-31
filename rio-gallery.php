<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
function rio_rpgs_gallery() { ?>
  <?php
  /* Start Create Gallery  */
  if(isset($_POST['submit']) && !empty($_POST['gallery_name'])) {
    if(wp_verify_nonce($_POST['cret_nonce'],'rpgs-nonce'.$_POST['galry_date'])){

      global $wpdb;
      $tablename=$wpdb->prefix.'rio_gallery';
      $string = preg_replace('/\s+/', '-', sanitize_text_field($_POST[ 'gallery_name']));
      $data=array(
        'gallery_name' => sanitize_text_field($_POST[ 'gallery_name']),
        'gallery_slug' => sanitize_text_field(strtolower($string)),
        'created_at'=>date('Y-m-d H:i:s')
      );
      $status=$wpdb->insert( $tablename, $data);
      if($status){
        echo '<div class="alert alert-success" style="width:40%; float:right"> Successfully Created </div>';

      } else{
        echo '<div class="alert alert-danger" style="width: 40%; float:right">Creation Failed </div>';
      }
    } else {
      echo '<div class="alert alert-danger" style="width: 40%; float:right">Invalid Access </div>';

    }
  }

  ?>
  <h2 class="wp-heading-inline mntitle1"> Add Gallery</h2>
  <form class="form-inline addnew-post" action="" method="POST">
    <?php
    $date=Date('Y-m-d H:i:s');
    $once= wp_create_nonce('rpgs-nonce'.$date);
    ?>
    <div class="form-group">
      <label for="Add New Gallery">Add New Gallery:</label>
      <input type="text" placeholder="Enter Gallery Name" class="form-control" id="gallery_name" name="gallery_name" required/>
      <input type="hidden" name="galry_date" value="<?php echo $date; ?>">
      <input type="hidden" name="cret_nonce" value="<?php echo $once; ?>">
    </div>
    <input type="submit"  class="btn btn-primary" name="submit" value="Add">
  </form>
  <!-- End Create Gallery -->

  <!-- Start View Galleries -->
  <div class="wrap">
    <table id="example" class="wp-list-table widefat fixed striped posts" style="">
      <thead>
        <tr>
          <th>Slno.</th>
          <th>Title</th>
          <th>Short Code</th>
          <th>Albums</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        global $wpdb;
        $i=1;
        $table_gallery=$wpdb->prefix.'rio_gallery';
        $table_album=$wpdb->prefix.'rio_album';
        $query=$wpdb->prepare("SELECT *,(SELECT Count(album_id) FROM $table_album WHERE $table_album.gallery_id=$table_gallery.gallery_id) as albumcount FROM $table_gallery",[]);
        $galleries = $wpdb->get_results($query);
        foreach ( $galleries as $gallery ) {
          ?>
          <tr id="gallery1">
            <td><?php echo $i; ?></td>
            <td><?php echo stripslashes($gallery->gallery_name); ?></td>
            <td>[rio-gallery id="<?php echo $gallery->gallery_id; ?>"]</td>
            <td><?php echo $gallery->albumcount; ?></td>
            <td>
              <a href="<?php echo admin_url('admin.php?page=individual-albums&id='.$gallery->gallery_id); ?>" class="btn-xs btn btn-success"> View </a>
              <button id="gallery-id" data-id="<?php echo $gallery->gallery_id; ?>" data-name="<?php echo $gallery->gallery_name; ?>" type="button" class="btn-xs btn btn-info editgalry">Edit</button>
              <button type="button" class="btn-xs btn btn-danger dltgallery" data-id="<?php echo $gallery->gallery_id;?>">Delete</button>
            </td>
          </tr>
          <?php $i++; } ?>
        </tbody>
      </table>
    </div>
    <!-- End View Galleries -->

    <!-- Start Edit Gallery -->
    <div class="modal fade" id="myModal" role="dialog">
      <div class="modal-dialog">
        <form action="" method="post" id="edit_gallery_form">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Edit Gallery</h4>
            </div>
            <div class="modal-body">
              <?php
              $date=Date('Y-m-d H:i:s');
              $once= wp_create_nonce('rpgs-nonce'.$date);
              ?>
              <div class="form-group">
                <label>Gallery Name:</label>
                <input id="edit_name" type="text" required class="form-control" value="" name="gname">
                <input id="edit_id" type="hidden" name="" value="">
                <input type="hidden" id="galry_date" name="galry_date" value="<?php echo $date; ?>">
                <input type="hidden" id="cret_nonce" name="cret_nonce" value="<?php echo $once; ?>">
              </div>

              <div class="text-right">
                <button id="gallery_edit" type="button" class="btn-sm btn btn-success" name="button"> Save</button>
                <button type="button" class="btn-sm btn btn-danger" data-dismiss="modal">Close</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
    <!-- End Edit Gallery -->

    <!-- Start Delete Gallery -->
    <div class="modal fade" id="Modaldelete" role="dialog">
      <?php
      $date=Date('Y-m-d H:i:s');
      $once= wp_create_nonce('rpgs-nonce'.$date);
      ?>
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <div class="modal-body">
            <center>  <h4 class="">Delete Gallery.?</h4>
              <br>
              <input type="hidden" name="" value="" id="delete_id">
              <input type="hidden" id="galry_date" name="galry_date" value="<?php echo $date; ?>">
              <input type="hidden" id="cret_nonce" name="cret_nonce" value="<?php echo $once; ?>">
              <button type="button" class="btn-sm btn btn-success gallerydlt">Yes</button>
              <button type="button" data-dismiss="modal" class="btn-sm btn btn-danger">No</button>
            </center>
          </div>

        </div>
      </div>
    </div>
    <!--  End Delete Gallery -->

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
    jQuery(document).ready(function(){
      jQuery('.editgalry').click(function(){
        jQuery('#myModal').modal('show');
        var dataid=jQuery(this).attr('data-id');
        var dataname=jQuery(this).attr('data-name');
        jQuery('#edit_name').val(dataname);
        jQuery('#edit_id').val(dataid);
      })
    });
    </script>

    <script type="text/javascript">
    jQuery(document).on('click','.dltgallery',function(){
      jQuery('#Modaldelete').modal('show');
      var dataid=jQuery(this).attr('data-id');
      jQuery('#delete_id').val(dataid);
    })
    </script>

    <script type="text/javascript">
    jQuery(document).ready(function() {
      jQuery('.example').DataTable();
    });
    </script>

    <?php
  }
  add_action( 'admin_footer', 'edit_gallery_javascript' ); // Write our JS below here
  function edit_gallery_javascript() { ?>
    <script type="text/javascript">

    jQuery(document).ready(function($) {
      // jQuery(document).on('submit','#gallery_update',function(e){
      //   e.preventDefault();
      jQuery(document).on('click',"#gallery_edit",function(){
        var data = {
          'action': 'edit_gallery_action',
          'data-id': jQuery('#edit_id').val(),
          'data-name':jQuery('#edit_name').val(),
          'galry_date':jQuery('#galry_date').val(),
          'cret_nonce':jQuery('#cret_nonce').val(),
        };
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function(response) {
          //  alert('Got this from the server: ' + response);
          var obj=$.parseJSON(response);
          if(obj.status){
            $.notify({
              // options
              message: 'Successfully Updated'
            },{
              // settings
              type: 'success'
            });
            window.setTimeout(function(){
              location.reload();
            },500)
          } else {
            $.notify({
              // options
              message: 'updation Faild'
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
    </script>
    <?php
  }

  add_action( 'wp_ajax_edit_gallery_action', 'edit_gallery_action' );
  function edit_gallery_action() {
    global $wpdb;
    $id=intval($_POST['data-id']);
    $gallery_name=sanitize_text_field($_POST['data-name']);
    $table_gallery=$wpdb->prefix.'rio_gallery';
    $string = preg_replace('/\s+/', '-',sanitize_text_field($_POST['data-name']));
    if(wp_verify_nonce($_POST['cret_nonce'],'rpgs-nonce'.$_POST['galry_date'])){
      $data=array(
        'gallery_name'=>$gallery_name,
        'gallery_slug'=>strtolower($string),
        'created_at'=>date('Y:m:d H:i:s')
      );
      $matchThese=['gallery_id'=>$id];
      $response=$wpdb->update($table_gallery,$data,$matchThese);
      if($response>0){
        $result['status']=true;
      } else {
        $result['status']=false;
      }
    } else {
      $result['status']=false;
    }
    echo json_encode($result);
    wp_die();
  }

  ?>
