<?php

$thumb_width = $row->gallery_thumb_custom_size ? $row->gallery_thumb_custom_size : 100;
$ratio = 1280 / 720;

$thumb_height = $thumb_width / $ratio;

if(ytwd_get_option("gdpr") == 1 && empty($_COOKIE["ytwd_gdpr_accept"])){
    ?>
    <div style="padding:11px;border: 1px solid #f5f5f5;">
        <div><?php  echo str_replace('[your site]', get_bloginfo(), ytwd_get_option("gdpr_text"));?></div>
        <div style="text-align:center;">
            <label for="ytwd_accept_privacy<?php echo $shortcode_id;?>"><?php _e("I accept", "ytwd"); ?>
                &nbsp;<input type="checkbox" name="ytwd_accept_privacy<?php echo $shortcode_id;?>" id="ytwd_accept_privacy<?php echo $shortcode_id;?>" value="1">
            </label>
            <button class="ytwd_policy_continue<?php echo $shortcode_id;?> ytwd-btn" style="display:none"><?php _e("Continue", "ytwd"); ?></button>
        </div>
    </div>
    <script>
        jQuery( document ).ready(function() {
            jQuery("[name=ytwd_accept_privacy<?php echo $shortcode_id;?>]").change(function(){
                if(jQuery(this).is(":checked")){
                    jQuery(".ytwd_policy_continue<?php echo $shortcode_id;?>").show();
                } else{
                    jQuery(".ytwd_policy_continue<?php echo $shortcode_id;?>").hide();
                }
            });
            jQuery(".ytwd_policy_continue<?php echo $shortcode_id;?>").click(function(){
                if(jQuery("[name=ytwd_accept_privacy<?php echo $shortcode_id;?>]").is(":checked")){
                    var data = {};
                    data["action"] = "accept_gdpr";
                    data["task"] = "accept_gdpr";
                    jQuery.post("<?php echo admin_url('admin-ajax.php');?>", data, function (response){
                        location.reload();
                    });
                }
            })
        });

    </script>
    <?php

} else { ?>
<div class="ytwd_container_wrapper ytwd_container_wrapper<?php echo $shortcode_id; ?>">
    <div class="ytwd_container">
        <div id="ytwd_container_1">
            <div id="ytwd_container_2" <?php echo isset($_GET["f_p"]) || isset($_GET["item_id"]) ? 'style="padding: 10px;"' : ''; ?>>
            <?php
                if($row->embed_type != 0 && $row->channel_additional_info){
                    require(YTWD_DIR. "/views/view_channel_header.php");
                }

                if($row->enable_gallery == 1 && $row->gallery_position == 1){
                    require(YTWD_DIR. "/views/view_gallery_frontend.php");
                }
        
				require(YTWD_DIR. "/views/view_iframe_frontend.php");
                
                if($row->enable_gallery == 1 && $row->gallery_position == 0){
                    require(YTWD_DIR. "/views/view_gallery_frontend.php");
                }
                ?>

            </div>
        </div>
    </div>
</div>
<script>
    if(typeof ytwdData == 'undefined'){
        var ytwdData = {};
    }
    ytwdData["ID" + "<?php echo $shortcode_id; ?>"] = "<?php echo $row->id; ?>";
    ytwdData["embedType" + "<?php echo $shortcode_id; ?>"] = "<?php echo $row->embed_type; ?>";
    ytwdData["enableGallery" + "<?php echo $shortcode_id; ?>"] = "<?php echo $row->enable_gallery; ?>";
    ytwdData["youtube_id" + "<?php echo $shortcode_id; ?>"] = "<?php echo $youtube_id; ?>";
    ytwdData["videoQuality" + "<?php echo $shortcode_id; ?>"] = "<?php echo $row->video_quality; ?>";
    ytwdData["initialVolume" + "<?php echo $shortcode_id; ?>"] = "<?php echo $row->initial_volume; ?>";
    ytwdData["thumbsColumnCount" + "<?php echo $shortcode_id; ?>"] = "<?php echo $row->thumbnails_column_number; ?>";
    ytwdData["firstClick" + "<?php echo $shortcode_id; ?>"] = true;
    ytwdData["loadingEffects" + "<?php echo $shortcode_id; ?>"] = "<?php echo $row->loading_effects; ?>";
    ytwdData["totalPages" + "<?php echo $shortcode_id; ?>"] = Number("<?php echo $total_results; ?>");
    ytwdData["itemsCountPerPage" + "<?php echo $shortcode_id; ?>"] = Number("<?php echo $row->gallery_items_count; ?>"); 
	  ytwdData["thumbsWidth" + "<?php echo $shortcode_id; ?>"] = "<?php echo $thumb_width; ?>";
    ytwdData["videoAdditionalInfo" + "<?php echo $shortcode_id; ?>"] = "<?php echo $row->video_additional_info; ?>";

    jQuery(window).on("load",function() {
        youTubeReadyFunction('<?php echo $shortcode_id; ?>');
    });
</script><?php
}
