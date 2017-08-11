<?php
add_action('admin_init', 'ebay_seller_admin_init');
add_action('admin_menu', 'ebay_seller_add_sub_menu_page' );

function ebay_seller_admin_init() {
	wp_register_style('ebay_seller_back.css', EBAY_SELLER_PLUGIN_URL . 'back.css');
	wp_enqueue_style('ebay_seller_back.css');
}

function ebay_seller_admin_configuration() {
  $page_content = "";
  $page_content .= '<div class="ebay_seller">';
  $page_content .=  '<h2>'.__("EBay Seller - Configuration.").'</h2>';
  $data = array();
  if(isset($_POST['submit']) && isset($_POST['ebay_configuration'])){
    $data = $_POST['ebay_configuration'];
    
    update_option(EBAY_SELLER_PLUGIN_VAR_NAME, base64_encode(serialize($data)));
    $page_content .= '<div class="announce">'.__("Successfully updated").'</div>';
  } else {
    $data = get_option(EBAY_SELLER_PLUGIN_VAR_NAME, array());
    if(is_string($data))
      $data = unserialize(base64_decode($data));
  }

  $page_content .=  ebay_seller_get_form($data);
  $page_content .= "</div>";

  echo $page_content;
}

function ebay_seller_add_sub_menu_page(){
  if ( function_exists('add_submenu_page') )
    add_submenu_page('plugins.php', __('EBay Seller Configuration'), __('EBay Seller'), 'manage_options', 'ebay-seller-config', 'ebay_seller_admin_configuration');
}

function ebay_seller_get_form($form_values = array()){
    // Prevent invalid $_POST .
    if(!is_array($form_values))
      exit(__("Invalid form values"));

    $ebay_proxy_language_list = array(
        "en" => "en - GB",
        "es" => "es - ES",
        "fr" => "fr - FR",
        "it" => "it - IT",
        "de" => "de - DE",
    );

    $ebay_source_site = array(
                            "0"   => "USA",
                            "2"   => "Canada",
                            "3"   => "United Kingdom",
                            "15"  => "Australia",
                            "16"  => "Austria",
                            "71"  => "France",
                            "77"  => "Germany",
                            "101" => "Italy",
                            "146" => "Netherlands",
                            "186" => "Spain",
                            "193" => "Switzerland",
                            "205" => "Ireland",
                            "23"  => "Belgium-fr",
                            "123" => "Belgium-nl",
                          );

    $ebay_item_type = array(
          "AllItemTypes"        => "AllItemTypes",
          "AuctionItemsOnly"    => "AuctionItemsOnly",
          "FixedPricedItem"     => "FixedPricedItem",
          "StoreInventoryOnly"  => "StoreInventoryOnly"
    );

    $ebay_item_sort = array(
          "BidCount"          => "BidCount",
          "EndTime"           => "EndTime",
          "PricePlusShipping" => "PricePlusShipping",
          "CurrentBid"        => "CurrentBid"
    );
    
    $ebay_displaydate = array(
          "true"          => "yes",
          "false"           => "no"
    );
    
    $ebay_floatorder = array(
          "floating"          => "floating",
          "Regular"           => "regular"
    );
    

    $ebay_open_list = array(
            '_blank'  =>  "new window",
            '_self'   =>  "same window",
    );
  
    $ret = "";
    $ret .= '<form class="ebay_seller" method="post">';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("SellerID").'</label>';
    $ret .= ' <input type="text" value="%%%sellerID%%%" name="ebay_configuration[sellerID]">';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Ebay country source").'</label>';
    $ret .=   ebay_seller_generateSelectFromArray($ebay_source_site, 'ebay_configuration[sourceSite]', isset($form_values['sourceSite']) ? $form_values['sourceSite'] : "");
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Maximum items").'</label>';
    $ret .=   '<input type="text" value="%%%maxEntries%%%" name="ebay_configuration[maxEntries]">';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Type of items").'</label>';
    $ret .=   ebay_seller_generateSelectFromArray($ebay_item_type, 'ebay_configuration[itemType]', isset($form_values['itemType']) ? $form_values['itemType'] : "");
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Enter your intro text").'</label>';
    $ret .=   '<textarea name="ebay_configuration[intro]">'."%%%intro%%%".'</textarea>';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Sort order").'</label>';
    $ret .=   ebay_seller_generateSelectFromArray($ebay_item_sort, 'ebay_configuration[itemSort]', isset($form_values['itemSort']) ? $form_values['itemSort'] : "");
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Category ID from eBay (optional)").'</label>';
    $ret .=   '<input type="text" value="%%%categoryId%%%" name="ebay_configuration[categoryId]">';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Open ebay link").'</label>';
    $ret .=   ebay_seller_generateSelectFromArray($ebay_open_list, 'ebay_configuration[openlink]', isset($form_values['openlink']) ? $form_values['openlink'] : "");
    $ret .= '</div>';
    
    
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("<b>floating auctions</b> (responsive rows and columns on full available page) or <b>regular column version</b>").'</label>';
    $ret .=   ebay_seller_generateSelectFromArray($ebay_floatorder, 'ebay_configuration[floatorder]', isset($form_values['floatorder']) ? $form_values['floatorder'] : "");
    $ret .= '</div>';
    
     
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("<b>Display date on floating version</b>").'</label>';
    $ret .=   ebay_seller_generateSelectFromArray($ebay_displaydate, 'ebay_configuration[displaydate]', isset($form_values['displaydate']) ? $form_values['displaydate'] : "");
    $ret .= '</div>';
    
    
    
    
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Display language").'</label>';
    $ret .=   ebay_seller_generateSelectFromArray($ebay_proxy_language_list, 'ebay_configuration[proxy_display_language]', isset($form_values['proxy_display_language']) ? $form_values['proxy_display_language'] : "");
    $ret .= '</div>';
    $ret .= '<div class="clear"></div>';
    $ret .= '<input type="submit" name="submit" value="Save"/>';
    $ret .= '</form>';

    $ret = str_replace("%%%sellerID%%%", isset($form_values['sellerID']) ? $form_values['sellerID'] : "", $ret);
    $ret = str_replace("%%%maxEntries%%%", isset($form_values['maxEntries']) ? $form_values['maxEntries'] : "3", $ret);
    $ret = str_replace("%%%categoryId%%%", isset($form_values['categoryId']) ? $form_values['categoryId'] : "", $ret);
    $ret = str_replace("%%%intro%%%", isset($form_values['intro']) ? $form_values['intro'] : "", $ret);
    $ret = str_replace("%%%bidSentance%%%", isset($form_values['bidSentance']) ? $form_values['bidSentance'] : "", $ret);
    $ret = str_replace("%%%couponcode%%%", isset($form_values['couponcode']) ? $form_values['couponcode'] : "", $ret);

    return $ret;
}

function ebay_seller_generateSelectFromArray($options , $select_name , $selected_option = null){
    $return = "";
    $return .= '<select id="'.$select_name.'" name="'.$select_name.'">';
    foreach($options as $value=>$name){
        $return .= '<option value="'.$value.'"';

        if($value == $selected_option)
            $return .= 'selected="selected"';

        $return .= '>'.$name.'</option>';
    }
    $return .= '</select>';

    return $return;
}
