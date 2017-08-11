<?php
/**
 * @package EBay Plugins
 */

function widget_ebay_seller_register() {
  function widget_ebay_seller($args) {
      $params = get_option(EBAY_SELLER_PLUGIN_VAR_NAME, array());
      if(is_string($params))
        $params = unserialize(base64_decode($params));
    
      if(!is_array($params) || empty($params)){
        echo __("EBay Seller is not properly configured");
        return;
      }
      if(trim($params['sellerID']) == ""){
        echo __("EBay Seller is not properly configured, missing seller id");
        return;
      }
      $content = '<div id="ebay_seller">';

      if(isset($params['intro']) && trim($params['intro']) != "")
        $content .= "<p>".str_replace(array("\r\n", "\n"),"<br>",$params['intro'])."</p>";
   
      $content .=   ebay_seller_generate_javascript($params);
      $content .= '</div>';
      echo $content;
  }

  function ebay_seller_generate_javascript($params) {
    $callUrl = ebay_seller_get_proxy_url();

    $resp = array(
            'requestType' => 'EBaySeller',
            'sellerID'    => urlencode($params['sellerID']),
            'maxEntries'  => isset($params['maxEntries']) ? $params['maxEntries'] : '3',
            'categoryId'  => isset($params['categoryId']) ? $params['categoryId'] : '',
            'itemSort'    => isset($params['itemSort']) ? $params['itemSort'] : 'EndTime',
            'floatorder'  => isset($params['floatorder']) ? $params['floatorder'] : 'regular',
            'displaydate' => isset($params['displaydate']) ? $params['displaydate'] : 'yes',
            
            'item.filter(0).name'    	=> isset($params['itemType']) ? $params['itemType'] : 'AllItemTypes',
			'item.filter(0).value(0)'   => isset($params['itemType']) ? $params['itemType'] : 'All',
                        
            
            'sourceSite'  => isset($params['sourceSite']) ? $params['sourceSite'] : '0',
            'proxy_display_language'  => isset($params['proxy_display_language']) ? $params['proxy_display_language'] : 'en',
        );

    $bid_sentance = isset($params['bidSentance']) ? $params['bidSentance'] : false;

    if ($bid_sentance != false && $bid_sentance !== ""){
      $resp['bid_sentance'] = $bid_sentance;
    }
    
    $first = true;
    foreach ($resp as $key=>$param){
        if($first){
           $first = false;
           $callUrl .= $key . '=' . $param;
        } else
           $callUrl .= '&' . $key . '=' . $param;
    }


    return '<script type="text/javascript" src="'.$callUrl.'"></script>';
  }

  function widget_ebay_seller_control(){
      $content = "";
      $content .= __('Please configure your widget from');
      $content .= ': <a href="plugins.php?page=ebay-seller-config">';
      $content .= __("here");
      $content .= '</a>';

      echo $content;
  }

  function widget_ebay_seller_include_css(){
      echo '<style type="text/css">'.file_get_contents(EBAY_SELLER_PLUGIN_URL."front.css").'</style>';
  }

  if(function_exists('register_sidebar_widget') ){
    if(function_exists('wp_register_sidebar_widget')){
      wp_register_sidebar_widget( 'ebay_seller', 'EBay Seller', 'widget_ebay_seller', null, 'ebay_seller');
      wp_register_widget_control( 'ebay_seller', 'EBay Seller', 'widget_ebay_seller_control', null, 75, 'ebay_seller');
    }elseif(function_exists('register_sidebar_widget')){
      register_sidebar_widget('EBay Seller', 'widget_ebay_seller', null, 'ebay_seller');
      register_widget_control('EBay Seller', 'widget_ebay_seller_control', null, 75, 'ebay_seller');
    }
  }
  
  if(is_active_widget('widget_ebay_seller'))
    add_action('wp_head', 'widget_ebay_seller_include_css');

}

add_action('init', 'widget_ebay_seller_register');

