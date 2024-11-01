<?php
function ytwd_get_option($name)
{
    $settings = json_decode(get_option("ytwd_settings"), true);
    return (isset($settings[$name]) ? $settings[$name] : false);
}

function ytwd_get_youtube_embed_id($_url)
{
    $id = false;
    $url = parse_url($_url);
    if (isset($url['host'])) {
        if (strcasecmp($url['host'], 'youtu.be') === 0) {
            $id = substr($url['path'], 1);
        } else if (strcasecmp($url['host'], 'www.youtube.com') === 0) {
            if (isset($url['query'])) {
                parse_str($url['query'], $url['query']);
                if (isset($url['query']['v'])) {
                    $id = $url['query']['v'];
                } else if (isset($url['query']['list'])) {
                    $id = $url['query']['list'];
                }
            }
            if ($id == false) {
                $url['path'] = explode('/', substr($url['path'], 1));
                if (in_array($url['path'][0], array('e', 'embed', 'v'))) {
                    $id = $url['path'][1];
                }
            }
        }
    } else {
        $api_data = new YouTube_API_Data();
        $video_data = $api_data->search_api_data(urlencode($_url));
        if ($video_data) {
            $id = isset($video_data["items"][0]["id"]["videoId"]) ? $video_data["items"][0]["id"]["videoId"] : 0;
        }
    }

    return $id;
}

function ytwd_upgrade_pro($text = false)
{
    $page = isset($_GET["page"]) ? sanitize_text_field($_GET["page"]) : "";
    $task = isset($_GET["task"]) ? sanitize_text_field($_GET["task"]) : "";
    ?>
    <div class="wrap">
		<div class="ytwd_upgrade-container">
			<div class="ytwd-topbar ytwd-topbar-content">
				<div class="ytwd-topbar-content-container">
					<div class="ytwd-topbar-content-title">10Web YouTube Premium</div>
					<div class="ytwd-topbar-content-body">Easily embed YouTube Videos, Channels and Playlists on Your Website</div>
				</div>
				<div class="ytwd-topbar-content-button-container">
					<a href="https://10web.io/plugins/wordpress-youtube/" target="_blank" class="ytwd-topbar-upgrade-button">Upgrade</a>
				</div>
			</div>
			<div class="ytwd-topbar ytwd-topbar-links">
				<div class="ytwd-topbar-links-container">
					<a href="https://help.10web.io/hc/en-us/articles/360017855912-Configuring-YouTube-API-Key" target="_blank">
						<div class="ytwd-topbar-links-item">User guide</div>
					</a>
					<span class="ytwd-topbar-separator"></span>
					<a href="https://wordpress.org/support/plugin/wd-youtube/" target="_blank">
						<div class="ytwd-topbar-links-item">Support Forum </div>
					</a>
				</div>
			</div>
		</div>
	</div>
    
    <?php if ($text) { ?>
		<div class="wd-text-right wd-row" style="color: #15699F; font-size: 20px; margin-top:10px; padding:0px 15px;">
			<?php echo sprintf(__("This is FREE version, Customizing %s is available only in the PAID version.", "gmwd"), $text); ?>
		</div>
    <?php
	}
}

function ytwd_message($message, $type)
{
    echo '<div style="width:99%"><div class="' . $type . '"><p><strong>' . $message . '</strong></p></div></div>';
}

function ytwd_sorting_data($data, $order_by, $order_dir)
{

    for ($i = 0; $i < count($data); $i++) {
        for ($j = $i + 1; $j < count($data); $j++) {
            if (($order_dir == "asc" && $data[$j][$order_by] < $data[$i][$order_by]) || ($order_dir == "desc" && $data[$j][$order_by] > $data[$i][$order_by])) {
                $y = $data[$i];
                $data[$i] = $data[$j];
                $data[$j] = $y;
            }
        }

    }

    return $data;

}

function ytwd_print_message()
{
    $message_id = isset($_GET["message_id"]) ? sanitize_text_field($_GET["message_id"]) : "";
    if (!ctype_digit($message_id) && $message_id) {
        echo '<div style="width:99%"><div class="error"><p><strong>' . esc_html(stripslashes($message_id)) . '</strong></p></div></div>';

        return;
    }
    switch ($message_id) {
        // save apply
        case "1":
            echo '<div style="width:99%"><div class="updated"><p><strong>' . __("Item Succesfully Saved.", "ytwd") . '</strong></p></div></div>';
            break;
        // save as copy
        case "2":
            echo '<div style="width:99%"><div class="updated"><p><strong>' . __("Item Succesfully Duplicated.", "ytwd") . '</strong></p></div></div>';
            break;
        // dublicate
        case "3":
            echo '<div style="width:99%"><div class="updated"><p><strong>' . __("Item(s) Succesfully Duplicated.", "ytwd") . '</strong></p></div></div>';
            break;
        //remove
        case "4":
            echo '<div style="width:99%"><div class="updated"><p><strong>' . __("Item(s) Succesfully Removed.", "ytwd") . '</strong></p></div></div>';
            break;
        //publish
        case "5":
            echo '<div style="width:99%"><div class="updated"><p><strong>' . __("Item(s) Succesfully Published.", "ytwd") . '</strong></p></div></div>';
            break;
        //unpublish
        case "6":
            echo '<div style="width:99%"><div class="updated"><p><strong>' . __("Item(s) Succesfully Unublished.", "ytwd") . '</strong></p></div></div>';
            break;
        // one item
        case "7":
            echo '<div style="width:99%"><div class="error"><p><strong>' . __("You Must Select At Least One Item. ", "ytwd") . '</strong></p></div></div>';
            break;
        // import
        case "8":
            echo '<div style="width:99%"><div class="updated"><p><strong>' . __("Successfully Imported.", "ytwd") . '</strong></p></div></div>';
            break;
        // unexepted file
        case "9":
            echo '<div style="width:99%"><div class="error"><p><strong>' . __("Unexepted File.", "ytwd") . '</strong></p></div></div>';
            break;
        case "10":
            echo '<div style="width:99%"><div class="updated"><p><strong>' . __("Options Succesfully Saved.", "ytwd") . '</strong></p></div></div>';
            break;
        case "11":
            echo '<div style="width:99%"><div class="updated"><p><strong>' . __("The Item Is Successfully Set as Default.", "ytwd") . '</strong></p></div></div>';
            break;
    }
}

function ytwd_redirect($url) {  ?>
    <script>
        window.location = "<?php echo $url; ?>";
    </script>
    <?php
    exit;
}

function ytwd_number_shorten($number, $precision = 1) {
    $divisors = array(
        pow(1000, 0) => '', // 1000^0 == 1
        pow(1000, 1) => 'K', // Thousand
        pow(1000, 2) => 'M', // Million
        pow(1000, 3) => 'B', // Billion
        pow(1000, 4) => 'T', // Trillion
    );

    foreach ($divisors as $divisor => $shorthand) {
        if ($number < ($divisor * 1000)) {
            break;
        }
    }
    if ($shorthand == "") {
        return $number;
    }

    return number_format($number / $divisor, $precision) . $shorthand;
}

function ytwd_search($search_by, $search_value, $form_id) { ?>
    <div class="alignleft actions">
        <script>
            function ytwd_search() {
                document.getElementById("page_number").value = "1";
                document.getElementById("search_or_not").value = "search";
                document.getElementById("<?php echo $form_id; ?>").submit();
            }
            function ytwd_reset() {
                if (document.getElementById("search_value")) {
                    document.getElementById("search_value").value = "";
                }
                if (document.getElementById("search_select_value")) {
                    document.getElementById("search_select_value").value = 0;
                }
                document.getElementById("<?php echo $form_id; ?>").submit();
            }
            function check_search_key(e, that) {
                var key_code = (e.keyCode ? e.keyCode : e.which);
                if (key_code == 13) { /*Enter keycode*/
                    spider_search();
                    return false;
                }
                return true;
            }
        </script>
        <div class="alignleft actions" style="">
            <label for="search_value" style="font-size:14px;  display:inline-block;"><?php echo $search_by; ?>:</label>
            <input type="text" id="search_value" name="search_value" onkeypress="return check_search_key(event, this);" value="<?php echo esc_html($search_value); ?>" style="width: 287px;"/>
        </div>
        <div class="alignleft actions ">
            <input type="button" value="" onclick="ytwd_search()" class="wd-search-btn">
            <input type="button" value="" onclick="ytwd_reset()" class="wd-reset-btn">
        </div>
        <div class="wd-clear"></div>
    </div>
    <?php
}

function ytwd_html_page_nav($count_items, $pager, $page_number, $form_id, $items_per_page = 20)
{
    $limit = $items_per_page;
    if ($count_items) {
        if ($count_items % $limit) {
            $items_county = ($count_items - $count_items % $limit) / $limit + 1;
        } else {
            $items_county = ($count_items - $count_items % $limit) / $limit;
        }
    } else {
        $items_county = 1;
    }
	
    if (!$pager) { ?>
        <script type="text/javascript">
            var items_county = <?php echo $items_county; ?>;

            function spider_page(x, y) {
                switch (y) {
                    case 1:
                        if (x >= items_county) {
                            document.getElementById('page_number').value = items_county;
                        }
                        else {
                            document.getElementById('page_number').value = x + 1;
                        }
                        break;
                    case 2:
                        document.getElementById('page_number').value = items_county;
                        break;
                    case -1:
                        if (x == 1) {
                            document.getElementById('page_number').value = 1;
                        }
                        else {
                            document.getElementById('page_number').value = x - 1;
                        }
                        break;
                    case -2:
                        document.getElementById('page_number').value = 1;
                        break;
                    default:
                        document.getElementById('page_number').value = 1;
                }
                document.getElementById('<?php echo $form_id; ?>').submit();
            }

            function check_enter_key(e, that) {
                var key_code = (e.keyCode ? e.keyCode : e.which);
                if (key_code == 13) { /*Enter keycode*/
                    if (jQuery(that).val() >= items_county) {
                        document.getElementById('page_number').value = items_county;
                    }
                    else {
                        document.getElementById('page_number').value = jQuery(that).val();
                    }
                    document.getElementById('<?php echo $form_id; ?>').submit();
                }
                return true;
            }
        </script>
    <?php } ?>
	
    <div class="tablenav-pages">
		<span class="displaying-num">
			<?php
			if ($count_items != 0) {
				echo $count_items; ?> item<?php echo(($count_items == 1) ? '' : 's');
			}
			?>
		</span>
        <?php
        if ($count_items > $items_per_page) {
			$first_page = "first-page";
			$prev_page = "prev-page";
			$next_page = "next-page";
			$last_page = "last-page";
			if ($page_number == 1) {
				$first_page = "first-page disabled";
				$prev_page = "prev-page disabled";
				$next_page = "next-page";
				$last_page = "last-page";
			}
			if ($page_number >= $items_county) {
				$first_page = "first-page ";
				$prev_page = "prev-page";
				$next_page = "next-page disabled";
				$last_page = "last-page disabled";
			}
			?>
			<span class="pagination-links">
				<a class="<?php echo $first_page; ?>" title="Go to the first page" href="javascript:spider_page(<?php echo $page_number; ?>,-2);">«</a>
				<a class="<?php echo $prev_page; ?>" title="Go to the previous page" href="javascript:spider_page(<?php echo $page_number; ?>,-1);">‹</a>
				<span class="paging-input">
					<span class="total-pages">
						<input class="current_page" id="current_page" name="current_page" value="<?php echo $page_number; ?>" onkeypress="return check_enter_key(event, this)" title="Go to the page" type="text" size="1"/>
					</span> of
					<span class="total-pages">
						<?php echo $items_county; ?>
					</span>
				</span>
				<a class="<?php echo $next_page ?>" title="Go to the next page" href="javascript:spider_page(<?php echo $page_number; ?>,1);">›</a>
				<a class="<?php echo $last_page ?>" title="Go to the last page" href="javascript:spider_page(<?php echo $page_number; ?>,2);">»</a>
			</span>
		<?php
		}
		?>	
    </div>
    <?php if (!$pager) { ?>
		<input type="hidden" id="page_number" name="page_number"
           value="<?php echo((isset($_POST['page_number'])) ? (int)esc_html(stripslashes($_POST['page_number'])) : 1); ?>"/>
		<input type="hidden" id="search_or_not" name="search_or_not"
           value="<?php echo((isset($_POST['search_or_not'])) ? esc_html(stripslashes(($_POST['search_or_not']))) : ''); ?>"/>
    <?php
	}
}

function ytwd_api_key_notice()
{

    echo '<div style="width:99%">
                <div class="error">
                    <p style="font-size:18px;"><strong>' . __("Important. API key is required for YouTube channels and playlists to work.", "ytwd") . '</strong></p>
                    <p><a name="" href="#" onClick="MyWindow=window.open(\'https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,directions_backend,distance_matrix_backend,elevation_backend,static_maps_backend,roads,street_view_image_backend,geolocation,places_backend&keyType=CLIENT_SIDE&reusekey=true\',\'MyWindow\',width=600,height=300); return false;"  class="wd-btn wd-btn-primary" style="text-decoration:none;">' . __("GET API KEY FOR FREE", "ytwd") . '</a></p>
					<p>' . __("For getting API key read more in", "ytwd") . '
						<a href="https://help.10web.io/hc/en-us/articles/360017855912-Configuring-YouTube-API-Key" target="_blank" style="color: #00A0D2;">' . __("User Manual", "ytwd") . '</a>.
					</p> 
                    <p>After creating the API key, please paste it here.</p>
                    <form method="post">
                        ' . wp_nonce_field('nonce_ytwd', 'nonce_ytwd') . '
                        <p>' . __("API Key", "ytwd") . ' <input type="text" name="ytwd_api_key_general"> <button class="wd-btn wd-btn-primary">' . __("Save", "ytwd") . '</button></p>
                        <input type="hidden" name="task" value="save_api_key">
                        <input type="hidden" name="page" value="' . esc_html(stripslashes($_GET["page"])) . '">
                    </form>
                </div>
          </div>';
}

function ytwd_get_shortcode_data (){
    global $wpdb;
    $shortcode_prefix = 'YouTube_WD';
    $rows = $wpdb->get_results("SELECT id, title as name FROM " . $wpdb->prefix . "ytwd_youtube WHERE published = '1' ORDER BY title");

    $data = array();
    $data['shortcode_prefix'] = $shortcode_prefix;
    $data['inputs'][] = array(
        'type'                    => 'select',
        'id'                      => $shortcode_prefix . '_id',
        'name'                    => $shortcode_prefix . '_id',
        'shortcode_attibute_name' => 'id',
        'options'                 => $rows,
    );

    return json_encode($data);

}

function validate_string($key , $default=""){
  if (isset($_GET[$key])) {
    $data = $_GET[$key];
  }
  elseif (isset($_POST[$key])) {
    $data = $_POST[$key];
  }
  elseif (isset($_REQUEST[$key])) {
    $data = $_REQUEST[$key];
  }
  else {
    $data = $default;
  }
  if(is_array($data)){
    $arr_data = array_map("sanitize_text_field", $data );
    return $arr_data;
  }
  $return_data = sanitize_text_field($data);
  return $return_data;
}

?>
