<?php 

/**
 * Get Customer id by customer_code Meta value
 */

function get_mb_customer_userid_using_email($email) {
    if (empty($email)) {
        return false;
    }

    $user_id = email_exists($email);

    return $user_id ? $user_id : false;
}