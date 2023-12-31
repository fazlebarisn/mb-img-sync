<?php 

/**
 * Function for fetch all products data from ICITEM table
 */
function fetch_all_data_from_j3_mijoshop_customer_api($page) {

    //$url = 'https://modern.cansoft.com/tables/ICITEM.php';
    $url = 'https://modern.cansoft.com/db-clone/api/j3-mijoshop-customer?key=58fff5F55dd444967ddkhzf&clone_status=All&perPage=100';
    //$url = 'https://modern.cansoft.com/db-clone/api/j3-mijoshop-product?key=58fff5F55dd444967ddkhzf&clone_status=All';

    $params = array(
        'page' => $page
    );

    $ch = curl_init();
    $url = add_query_arg($params, $url);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);

    if ($response === false) {
        // Handle the error if the request fails
        // You can log the error or implement retry logic here
        curl_close($ch);
        return null;
    }

    curl_close($ch);

    $data = json_decode($response, true);
    return $data;
}