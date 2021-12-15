<?php
/**
* @package KannanPlugin
*/
/*
Plugin Name: similar on sale plugin
Plugin URI: hhtps://google.com
Description: test test test tes
Version: 1.0.0
Author: Nik Kan
Author URI:google.com
License: GPLv2 or later
Text Domain: recently-viewd-plugin-kann
*/
if(!class_exists('SimilarOnSalePlugin')) {

    class SimilarOnSalePlugin
    {

        function __construct()
        {
            add_action('init', array($this, 'custom_post_type'));
            $this->plugin=plugin_basename(__FILE__);

            // echo $this->plugin;

        }
        function register(){
            add_action('admin_menu',array($this,'add_admin_pages'));
            add_shortcode( "nana","hoola");

        }
        function add_admin_pages(){
            add_menu_page('SimilarOnSalePlugin','Kann','manage_options','recently-viewd-plugin-kann',array($this,'admin_index'),'dashicons-store',100);
        }

        function activate()
        {
            flush_rewrite_rules();
            $this->custom_post_type();

        }

        function deactivate()
        {

        }

        function unistall()
        {

        }

        function custom_post_type()
        {
            register_post_type('book', ['public' => true, 'label' => "Books"]);
        }
        public function hoola(){
            echo "paaaaaaaaaaaaaaa";

        }

        function admin_index(){
            require_once plugin_dir_path(__FILE__).'templates/admin.php';
        }


    }


    if (class_exists('SimilarOnSalePlugin')) {
        $similarOnSalePlugin = new SimilarOnSalePlugin();
        $similarOnSalePlugin->register();

        //   $similarOnSalePlugin->rc_woocommerce_recently_viewed_products();
    }

    function rc_woocommerce_recently_viewed_products()
    {

        // Get WooCommerce Global
        global $woocommerce;








        // Get recently viewed product cookies data
        $viewed_products = !empty($_COOKIE['woocommerce_recently_viewed']) ? (array)explode('|', $_COOKIE['woocommerce_recently_viewed']) : array();
        $viewed_products = array_filter(array_map('absint', $viewed_products));

        if(empty($viewed_products)){
            echo "Not seen products yet";
            return ;
        }

        $product = wc_get_product($viewed_products[0]);




        // Get the current user Object
        $current_user = wp_get_current_user();

        // Check if the user is valid
   //     if ( 0 == $current_user->ID ) return;

        //Create $args array
        $args = array(
            'numberposts' => -1,
            'meta_key' => '_customer_user',
            'meta_value' => $current_user->ID,
            'post_type' => wc_get_order_types(),
            'post_status' => array_keys( wc_get_is_paid_statuses() ),
        );
        $product_in_cart_id = array();

        // Pass the $args to get_posts() function
        $customer_orders = get_posts( $args);
        foreach( WC()->cart->get_cart() as $cart_item ){
            $product_in_cart_id[] = $cart_item['product_id'];
        }



        // loop through the orders and return the IDs
        if ( ! $customer_orders ) {
            echo "heh";
        //    return;
        }
        $product_ids = array();
        foreach ( $customer_orders as $customer_order ) {
            $order = wc_get_order( $customer_order->ID );
            $items = $order->get_items();
            foreach ( $items as $item ) {
                $product_id = $item->get_product_id();
                $product_ids[] = $product_id;
            }
        }
        $product_ids = array_merge((array)$product_ids, (array)$product_in_cart_id);


        //      echo $product->get_title();
   //     echo wc_get_product_category_list($viewed_products[0]);
        $list = wc_get_product_category_list($viewed_products[0]);
     //   echo $list[3];
//            for ($b = 0; $b <= strlen($list); $b++) {
//                echo $list[$b];
//            }
//            $listt =wc_get_related_products($viewed_products[0])[$b];
//
//            for ($b = 0; $b <= count($listt); $b++) {
//                echo wc_get_related_products($viewed_products[0])[1];
//            }
        $in_cart_or_bought=false;
        $onSale = wc_get_product_ids_on_sale();
        //  echo $onSale[0];
        $arrayOfRelatedProducts = array();
        $isSmallList=false;
        for ($x = 0; $x < sizeof($viewed_products); $x++) {
            if(sizeof($viewed_products)<4){
                $isSmallList=true;
            }
            $product = wc_get_related_products($viewed_products[$x],20);
            shuffle($product);
            foreach($product as $result) {
                $product_category = wp_get_post_terms( $result, 'product_cat' );
                $product_category_viewing = wp_get_post_terms( $viewed_products[$x], 'product_cat' );
                if($product_category==$product_category_viewing)
                    array_unshift($product , $result);
//                foreach( $product_category as $cat ):
//                    if( 0 == $cat->child ) {
//                        if( $cat->name==
//                        break;
//                    }
//                endforeach;
//                echo '<br>';
            //    echo $result, $viewed_products[$x] , '<br>';
            }



            for ($b = 0; $b < sizeof($product); $b++) {

                $product_obj = wc_get_product($product[$b]);
                $in_cart_or_bought=false;
                for($v=0;$v<sizeof($product_ids);$v++) {
                    if ($product_ids[$v] == $product[$b]) {
                        $in_cart_or_bought=true;


                    }
                }
                if(!$in_cart_or_bought){
                if ($product_obj->is_on_sale()) {
                    if (!in_array($product_obj->get_id(), $arrayOfRelatedProducts)) {
                        array_push($arrayOfRelatedProducts, $product_obj->get_id());
                        if(!$isSmallList)
                        break;
                        else{
                            $isSmallList=false;
                        }
                        //      echo $product_obj->get_id() ;

                    }
                    }

                }
                //      if($product_obj)

                //if($product_obj->is_on_sale()){
                //    array_push($arrayOfRelatedProducts,$product[$b]);
                //  }
//                    echo $arrayOfRelatedProducts[0];
//                    echo $arrayOfRelatedProducts[1];
//                    echo $arrayOfRelatedProducts[2];


            }
      //      echo "\n";
//                $b= rand (  0 ,  sizeof(wc_get_related_products($viewed_products[$x]-1)) );
//                $product = wc_get_product( $viewed_products[0] );
//
//              $product=wc_get_related_products($viewed_products[$x])[$b];

//              if(!$product->is_on_sale())
//                  $x=$x-1;
////
//                $product = wc_get_product( $product );
//                if($product->is_on_Sale()){
//                    echo $product->get_title();
//                    }
//                if(!($product->is_on_sale())){
///
//
//                }

        }

        $arrayOfRelatedProducts=array_reverse($arrayOfRelatedProducts);
        for ($a = 0; $a < sizeof($arrayOfRelatedProducts); $a++) {
            $product = wc_get_product($arrayOfRelatedProducts[$a]);
            $name=$product->get_title();
            $image=wp_get_attachment_url( $product->get_image_id());
           $slug="http://localhost/wordpres/product/";
            $slug.=$product->get_slug();

            if ($product->is_type( 'simple' )) {
                $discount=$product->get_sale_price();
                $discount.='$';
                $price=$product->get_regular_price();
                $price=number_format($price, 2);
                $price.='$';

            }
            elseif($product->is_type('variable')){
                $discount     =  $product->get_variation_sale_price( 'min', true );
                $discount.='$';

                $price  =  $product->get_variation_regular_price( 'max', true );
                $price.='$';


            }



          //  <div class="first"></div>
           // echo <a href="default.asp"><img src='{$image}' alt="HTML tutorial" style="width:42px;height:42px;"></a>"<img src= alt='' width='150' height='150' ></a>";


            echo" <div class='left' style= 'width: 180px; height: 30px;' >
            <a href='{$slug}'><img src='{$image}' alt'' width='150' height='150' ></a>
            <h6 ><b> {$name}</b> </h6>
            <div ><del>from {$price} </del></div>
            <div >only<b> {$discount}</b></div>
            </div>";







        }

    }
    function prefix_add_my_stylesheet() {
        // Respects SSL, Style.css is relative to the current file
        wp_register_style( 'prefix-style', plugins_url('style.css', __FILE__) );
        wp_enqueue_style( 'prefix-style' );
    }

  //  add_action('init',array($this,'rc_woocommerce_recently_viewed_products'));

    add_shortcode('aha', 'rc_woocommerce_recently_viewed_products');
    add_action( 'wp_enqueue_scripts', 'prefix_add_my_stylesheet' );


    register_activation_hook(__FILE__, array($similarOnSalePlugin, 'activate'));
    register_deactivation_hook(__FILE__, array($similarOnSalePlugin, 'deactivate'));
}
