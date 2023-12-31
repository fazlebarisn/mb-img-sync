<?php 
/*
 * Plugin Name:       MB All Image Sync
 * Description:       This plugin synchronizes all Image from a database
 * Version:           0.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            CanSoft
 * Author URI:        https://cansoft.com/
 */
// Include your functions here
require_once( plugin_dir_path( __FILE__ ) . '/inc/all-functions/get_mb_customer_userid_using_email.php');

require_once( plugin_dir_path( __FILE__ ) . '/inc/api/j3_mijoshop_customer_api.php');


// Enqueue all assets
function mb_all_img_sync(){
    wp_enqueue_script('mbai-script', plugin_dir_url( __FILE__ ) . 'assets/js/script.js', null, time(), true);
}
add_action( 'admin_enqueue_scripts', 'mb_all_img_sync' );


/**
 * Add menu page for this plugin
 */
// function mb_all_img_sync_menu_pages(){
//     add_submenu_page( 'users.php', 'Mb All Img Sync', 'Mb All Img Sync ', 'manage_options', 'mbai-sync', 'mbai_sync_page' );
// }
// add_action( 'admin_menu', 'mb_all_img_sync_menu_pages' );

/**
 * Add a menu in wordpress product menu
 */
function mb_all_img_sync_menu_pages(){

    //add sub menu in product menu for sync product categories
    add_submenu_page(
        'mb_syncs',
        'Mb All Img Sync',
        'Mb All Img Sync',
        'manage_options',
        'mbai-sync',
        'mbai_sync_page'
    );
}
add_action('admin_menu', 'mb_all_img_sync_menu_pages', 999);


/**
 * Main function for product sync
 */
function mbai_sync_page(){
    ?>
    <style>
        .wrap .d-flex {
            display: flex;
            align-items: center;
            justify-content: space-evenly;
        }
    </style>
        <div class="wrap">
            <h1>This Page for Sincronize all product</h1><br>
            <div class="d-flex">
            	<form method="GET">
                    <input type="hidden" name="j3-upload-img" value="1">
                    <input type="hidden" name="page" value="mbai-sync">
                    <?php
                        submit_button('All Image Upload', 'primary', 'mb-all-img-upload');
                    ?>
                </form>

            	<form method="GET">
                    <input type="hidden" name="j3-sync-img" value="1">
                    <input type="hidden" name="page" value="mbai-sync">
                    <?php
                        submit_button('All Image LInk Sync', 'primary', 'mb-all-img-link-sync');
                    ?>
                </form>

            </div>
            <?php 

                /**
                 * After clicing All Image Upload button
                 * 
                 * For Main product making
                 */
                if(isset($_GET['j3-upload-img'])){

                    $i = $_GET['j3-upload-img'] ?? 1;
                    //$i = 1;

                    $allImage = fetch_all_data_from_j3_mijoshop_customer_api($i);
                    //dd($allProducts);
                    $start = microtime(true);
                    $api_ids = [];

                    $chunkarray = array_chunk($allImage, 10);

                    foreach ($chunkarray as $all_imgs) {
                    
                        foreach($all_imgs as $img_id){

                            /**
                             * Check Product not exit 
                             * 
                             * if product already exit than it will be not created as a product
                             */
                            
                            // Ensure the WordPress environment is loaded
                           // Ensure the WordPress environment is loaded
                            if (!function_exists('wp_upload_bits')) {
                                require_once(ABSPATH . 'wp-admin/includes/file.php');
                                require_once(ABSPATH . 'wp-admin/includes/media.php');
                                require_once(ABSPATH . 'wp-admin/includes/image.php');
                            }

                            // Source image URL
                            $baseUrl = 'https://www.modernbeauty.com/components/com_mijoshop/opencart/image/verification_files/';


                            $driversDoc = $img_id["drivers"];
                            $licenseDoc = $img_id["license"];

                            $upload_dir = wp_upload_dir();
                            if (!$licenseDoc) {
                                continue;
                            }

                            $imageSrcUrl = $baseUrl . $licenseDoc;
                            //dd($licenseDoc);

                            $destination_path = $upload_dir['basedir'] . '/' .$licenseDoc;



                            if (@copy($imageSrcUrl, $destination_path)) {
                                $api_ids[] = $img_id["id"];
                                echo "<span style='color:green'>Image moved successfully</span>";
                                echo "<br>";
                            }else{
                                echo "<span style='color:red'>Image not moved</span>";
                                echo "<br>";
                                echo $imageSrcUrl;
                                echo "<br>";

                            }
                            //dd($driversDoc);
                        }
                    }
                 
                    $total = microtime(true) - $start;
                    echo "Total Execution time: " . $total;

                    if(! count($allImage)){
                        wp_redirect( admin_url( "/users.php?page=mbai-sync" ) );
                        exit();
                    }
                }
                 /**
                 * After clicing All Image LInk Sync button
                 * 
                 * For Main product making
                 */
                if(isset($_GET['j3-sync-img'])){

                    $i = $_GET['j3-sync-img'] ?? 1;
                    //$i = 1;

                    $allImage = fetch_all_data_from_j3_mijoshop_customer_api($i);
                    //dd($allProducts);
                    $start = microtime(true);
                    $api_ids = [];

                    $chunkarray = array_chunk($allImage, 25);
                    
                    foreach ($chunkarray as $all_imgs) {
                    
                        foreach($all_imgs as $img_id){

                            /**
                             * Check Product not exit 
                             * 
                             * if product already exit than it will be not created as a product
                             */
                            
                            // Ensure the WordPress environment is loaded
                           // Ensure the WordPress environment is loaded
                            if (!function_exists('wp_upload_bits')) {
                                require_once(ABSPATH . 'wp-admin/includes/file.php');
                                require_once(ABSPATH . 'wp-admin/includes/media.php');
                                require_once(ABSPATH . 'wp-admin/includes/image.php');
                            }

                            // Source image URL
                            

                            // Disable email notifications
                            remove_action('user_register', 'wp_send_new_user_notifications', 10);
                            remove_action('edit_user_created_user', 'wp_send_new_user_notifications', 10);



                            $driversDoc = $img_id["drivers"];
                            $licenseDoc = $img_id["license"];

                            if (!$driversDoc) {
                                continue;
                            }

                            $existingUserId = user_exists_by_email_for_customer($img_id["email"]);

                            if ($existingUserId && !empty($driversDoc)) {

                                $imageUrl = site_url('/wp-content/uploads/customer-documents/' . $driversDoc);
                                //dd($imageUrl);

                                $attachment_id = wp_insert_attachment(array(
                                    //'post_title'     => 'Beauty License', // Change the title as needed
                                    'post_title'     => 'Govt Issued Photo Id', // Change the title as needed
                                    'post_type'      => 'attachment',
                                    'post_mime_type' => 'image/jpeg,image/jpg,application/pdf,application/doc,image/png', // Adjust the mime type if needed
                                    'guid'           => $imageUrl,
                                ), $imageUrl, $existingUserId);

                                if (!is_wp_error($attachment_id)) {
                                	
                                    //$result = update_user_meta($existingUserId, "beauty_license", $attachment_id);
                                    $result = update_user_meta($existingUserId, "government_issued_photo_id", $attachment_id);
                                    //dd($result);

                                    echo $img_id['id']." <span style='color:green; font-weight:bold'>Government issued photo id attachment successfully</span>";
                                    echo "<br>";
                                }else{
                                	echo 'Error: ' . $attachment_id->get_error_message();
                                    echo $img_id['id']." <span style='color:red; font-weight:bold'>Government issued photo id attachment unsuccessfull</span>";
                                    echo "<br>";
                                }
                            }else{
                                echo $img_id['id']." <span style='color:purple; font-weight:bold'>Government issued photo id not found</span>";
                                echo "<br>";
                            }
                        }
                    }
                 
                    $total = microtime(true) - $start;
                    echo "Total Execution time: " . $total;

                    if(! count($allImage)){
                        wp_redirect( admin_url( "/users.php?page=mbai-sync" ) );
                        exit();
                    }
                }

            ?>
        </div>
    <?php 
}
