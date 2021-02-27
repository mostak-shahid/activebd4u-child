<?php

function admin_moswallet_page(){
	//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null )
    add_menu_page( 
        __( 'Pending Wallet List', 'textdomain' ),
        'Wallet',
        'manage_options',
        'moswallet',
        'moswallet_page',
        'dashicons-vault',
        3
    ); 
}


add_filter( 'woocommerce_billing_fields', 'email_optional_field');

function email_optional_field( $fields ) {
    $fields['billing_last_name']['required'] = false;
    $fields['billing_email']['required'] = false;
    return $fields;
}

add_filter ( 'woocommerce_account_menu_items', 'misha_remove_my_account_links' );
function misha_remove_my_account_links( $menu_links ){
 
	//unset( $menu_links['edit-address'] ); // Addresses
 
 
	//unset( $menu_links['dashboard'] ); // Remove Dashboard
	//unset( $menu_links['payment-methods'] ); // Remove Payment Methods
	//unset( $menu_links['generations'] ); // Remove Orders
	unset( $menu_links['downloads'] ); // Disable Downloads
	//unset( $menu_links['edit-account'] ); // Remove Account details tab
	//unset( $menu_links['customer-logout'] ); // Remove Logout link
 
	return $menu_links;
 
}
add_filter( 'woocommerce_account_menu_items', 'add_my_menu_items', 99, 1 );


/*
 * Step 1. Add Link (Tab) to My Account menu
 */
function add_my_menu_items( $items ) {
    $my_items = array(
    //  endpoint   => label
        'generation-table' => __( 'Generation', 'my_plugin' ),
        'wallet-table' => __( 'Wallet', 'my_plugin' ),
        'transaction-table' => __( 'Transection Passworrd', 'my_plugin' ),
    );

    $my_items = array_slice( $items, 0, 1, true ) +
        $my_items +
        array_slice( $items, 1, count( $items ), true );

    return $my_items;
}

/*
 * Step 2. Register Permalink Endpoint
 */
add_action( 'init', 'misha_add_endpoint' );
function misha_add_endpoint() {
	add_rewrite_endpoint( 'generation-table', EP_PAGES ); 
	add_rewrite_endpoint( 'wallet-table', EP_PAGES );
	add_rewrite_endpoint( 'transaction-table', EP_PAGES );
}
/*
 * Step 3. Content for the new page in My Account, woocommerce_account_{ENDPOINT NAME}_endpoint
 */
add_action( 'woocommerce_account_generation-table_endpoint', 'misha_my_account_endpoint_content_generation_table' );
function misha_my_account_endpoint_content_generation_table() {
    global $wpdb; 
    $user_id = get_current_user_id();
    $generation = (@$_GET['generation'])?$_GET['generation']:1;
    $limit = (@$_GET['limit'] AND $_GET['limit'] > 0)?$_GET['limit']:1;
    $data = get_generation_ids($user_id, $generation, $limit);
    //var_dump($data);
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    echo '<div class="d-flex mb-15 generation-button-group">';
    for($x = 1; $x<=5; $x++){  
        $cls = '';
        if ($generation == $x) $cls = 'active';
        echo '<a class="flex-fill '.$cls.'" href="'.remove_query_strings_split($actual_link).'?generation='.$x.'&limit=1">Generation '.$x.'</a>';
    }   
    echo '</div>';
    echo '<div class="user-list">';
    if ($data['data']) {
        echo '<table class="woocommerce-generation-table woocommerce-MyAccount-generations shop_table shop_table_responsive my_account_generations account-generations-table">';
            echo '<thead>';
                echo '<tr>';
				    echo '<th class="woocommerce-generations-table__header"><span class="nobr">ID</span></th>';
					echo '<th class="woocommerce-generations-table__header"><span class="nobr">Username</span></th>';
				echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
                
                foreach($data['data'] as $value) {
                    $user_info = get_userdata($value);
                    echo '<tr class="woocommerce-generations-table__row woocommerce-generations-table__row--status-processing generation">';
                        echo '<td class="woocommerce-generations-table__cell woocommerce-generations-table__cell-generation-number" data-title="ID">'.$value.'</td>';
                        echo '<td class="woocommerce-generations-table__cell woocommerce-generations-table__cell-generation-number" data-title="Username">'.$user_info->user_login.'</td>';
                    echo '</tr>';
                }
            echo '</tbody>';
        echo '</table>';
        echo '<div class="d-table">';
            echo '<div class="d-table-cell">';
                $data_from = (($limit - 1) * 10) + 1;
                $data_to = $data_from + sizeof($data['data']) - 1;
                echo 'Showing '.$data_from.' to '.$data_to.' of '. $data["level_count"].' entries';
            echo '</div>';
            echo '<div class="d-table-cell text-right">';
                if ($limit>1)
                    echo '<a class="mos-prev-button mos-button" href="'.remove_query_strings_split($actual_link).'?generation='.$generation.'&limit='.($limit - 1).'">Prev</a>';
                if ($data_to<$data["level_count"])
                    echo '<a class="mos-next-button mos-button" href="'.remove_query_strings_split($actual_link).'?generation='.$generation.'&limit='.($limit + 1).'">Next</a>';  
            echo '</div>';
        echo '</div>';
    } else {
        echo 'No Data Found';
    }
    echo '</div>';
}

add_action( 'woocommerce_account_wallet-table_endpoint', 'misha_my_account_endpoint_content_wallet_table' );
function misha_my_account_endpoint_content_wallet_table() {
    global $wpdb;
    $user_id = get_current_user_id();
    $transaction_code = get_user_meta($user_id, '_mos_user_transaction_code', true);
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $limit = (@$_GET['limit'] AND $_GET['limit'] > 0)?$_GET['limit']:1;
    $data_start = (intval($limit)-1) * 10;
    
    $query = "SELECT meta_value FROM {$wpdb->prefix}usermeta WHERE user_id={$user_id} AND ( meta_key LIKE '%referal_income_from_%' OR meta_key LIKE '%cash_widraw%') ORDER BY umeta_id DESC LIMIT {$data_start},10";
    $data = $wpdb->get_results($query);
    $query2 = "SELECT COUNT(meta_value) FROM {$wpdb->prefix}usermeta WHERE user_id={$user_id} AND ( meta_key LIKE '%referal_income_from_%' OR meta_key LIKE '%cash_widraw%')";
    $max = $wpdb->get_var($query2);
    $query3 = "SELECT SUM(meta_value) AS sum FROM {$wpdb->prefix}usermeta WHERE user_id={$user_id} AND meta_key='wallet_amount'";
    $sum = $wpdb->get_var($query3);
    echo '<div class="user-list">';
    echo '<h4>Balance: ' . $sum .'</h4>';
    if ($data) {
        
        echo '<table class="woocommerce-generation-table woocommerce-MyAccount-generations shop_table shop_table_responsive my_account_generations account-generations-table">';
            echo '<thead>';
                echo '<tr>';
				    echo '<th class="woocommerce-generations-table__header"><span class="nobr">Date</span></th>';
					echo '<th class="woocommerce-generations-table__header"><span class="nobr">Type</span></th>';
					echo '<th class="woocommerce-generations-table__header"><span class="nobr">Amount</span></th>';
					echo '<th class="woocommerce-generations-table__header"><span class="nobr">Status</span></th>';
				echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
                
                foreach($data as $value) {
                    $val =  maybe_unserialize($value->meta_value);
                    //$user_info = get_userdata($value);
                    $type = ($val['amount']>0)?'Cashin':'Cashout';
                    echo '<tr class="woocommerce-generations-table__row woocommerce-generations-table__row--status-processing generation">';
                        echo '<td class="woocommerce-generations-table__cell woocommerce-generations-table__cell-generation-number" data-title="Date">'.$val['date'].'</td>';
                        echo '<td class="woocommerce-generations-table__cell woocommerce-generations-table__cell-generation-number" data-title="Type">'.$type.'</td>';
                        echo '<td class="woocommerce-generations-table__cell woocommerce-generations-table__cell-generation-number" data-title="Amount">'.$val['amount'].'</td>';
                        echo '<td class="woocommerce-generations-table__cell woocommerce-generations-table__cell-generation-number" data-title="Amount">'.$val['status'].'</td>';
                    echo '</tr>';
                }
            echo '</tbody>';
        echo '</table>';
        echo '<div class="d-table">';
            echo '<div class="d-table-cell">';
                $data_from = (($limit - 1) * 10) + 1;
                $data_to = $data_from + sizeof($data) - 1;
                echo 'Showing '.$data_from.' to '.$data_to.' of '. $max.' entries';
            echo '</div>';
            echo '<div class="d-table-cell text-right">';
                if ($limit>1)
                    echo '<a class="mos-prev-button mos-button" href="'.remove_query_strings_split($actual_link).'?limit='.($limit - 1).'">Prev</a>';
                if ($data_to<$max)
                    echo '<a class="mos-next-button mos-button" href="'.remove_query_strings_split($actual_link).'?limit='.($limit + 1).'">Next</a>';  
            echo '</div>';
        echo '</div>';
    } else {
        echo 'No Data Found';
    }
    echo '<hr style="margin-top:15px">';
    
    $msg = @$_GET['msg'];
    if ($msg == 'et1'){
        echo '<div class="woocommerce-notices-wrapper"><div class="woocommerce-message alert alert_success" role="alert"><div class="alert_icon"><i class="icon-check"></i></div><div class="alert_wrapper">Request accepted.</div></div>';
    } elseif ($msg == 'et1' OR $msg == 'et2'){
        echo '<div class="woocommerce-notices-wrapper"><div class="woocommerce-message alert alert_error" role="alert"><div class="alert_icon"><i class="icon-cancel"></i></div><div class="alert_wrapper">Please try again</div></div>';
    }
    echo '<h4>Cash Out</h4>';
    if (!$transaction_code) {
        echo '<h5>Please set your transection password first.</h5>';
    } else {
        
        echo '<p style="color: #eb3c70">Cashout charge 12%</p>';
        echo '<form name="cashoutform" id="cashoutform" action="" method="post">';
            echo '<p><label for="amount">Amount *</label><input type="number" name="amount" id="amount" class="input" placeholde="Amount" size="20" min="100" max="'.$sum.'" required></p>';
            echo '<p><label for="amount">Phone *</label><input type="text" name="phone" id="phone" class="input" placeholde="Amount" size="20" required></p>';
            echo '<p><label for="transaction_code">Transaction Code *</label><input type="password" name="transaction_code" id="transaction_code" class="input" placeholde="Transaction Code *" size="20" required></p>';
            echo '<p><label for="method">Method *</label><select name="method" class="input"><option>Bkash</option><option>Rocket</option><option>Nagad</option></select></p>';
            echo wp_nonce_field( 'mos_cashout_action', 'mos_cashout_field' );
            echo '<p class="submit"><button type="submit" class="button  button_size_1">Request</button></p>';
        echo '</form>';
    }
    echo '</div>';
}
if (isset( $_POST['mos_cashout_field'] ) && wp_verify_nonce( $_POST['mos_cashout_field'], 'mos_cashout_action' )) {     
    $user_id = get_current_user_id();
    $query3 = "SELECT SUM(meta_value) AS sum FROM {$wpdb->prefix}usermeta WHERE user_id={$user_id} AND meta_key='wallet_amount'";
    $sum = $wpdb->get_var($query3);
    
    $transaction_code = get_user_meta($user_id, '_mos_user_transaction_code', true);
    
    
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    
    if ($sum > $_POST['amount'] AND $transaction_code == $_POST['transaction_code']){
        $data = [
            'amount' => '-'.$_POST['amount'],
            'date' => date('Y-m-d'),
            'method' => $_POST['method'],
            'status' => 'pending',
            'phone' => $_POST['phone'],
        ];
        $user_id = get_current_user_id();
        add_user_meta( $user_id, 'wallet_amount', '-'.$_POST['amount']);
        add_user_meta( $user_id, 'cash_widraw',$data);        
        $link = remove_query_strings_split($actual_link) . '?msg=et1';
    } else {
        $link = remove_query_strings_split($actual_link) . '?msg=et2'; 
    }
    
    wp_redirect($link);
    exit;
}
function mysite_woocommerce_order_status_completed( $order_id ) {
    $order = wc_get_order( $order_id );
    $first_gen_com = $second_gen_com = $third_gen_com = $forth_gen_com = $fifth_gen_com = 0;
    foreach ( $order->get_items() as $item_id => $item ) {
        $product_id = $item->get_product_id();
        $first_gen_com += carbon_get_post_meta( $product_id, 'mos_product_first_gen_com' ) * $item->get_quantity();
        $second_gen_com += carbon_get_post_meta( $product_id, 'mos_product_second_gen_com' ) * $item->get_quantity();
        $third_gen_com += carbon_get_post_meta( $product_id, 'mos_product_third_gen_com' ) * $item->get_quantity();
        $forth_gen_com += carbon_get_post_meta( $product_id, 'mos_product_forth_gen_com' ) * $item->get_quantity();
        $fifth_gen_com += carbon_get_post_meta( $product_id, 'mos_product_fifth_gen_com' ) * $item->get_quantity();
    }
    
    $user = $order->get_user();
    
    $first_parent = get_user_meta($user->ID,'_mos_user_parent',true);
    $second_parent = get_user_meta($first_parent,'_mos_user_parent',true);
    $third_parent = get_user_meta($second_parent,'_mos_user_parent',true);
    $fourth_parent = get_user_meta($third_parent,'_mos_user_parent',true);
    $fifth_parent = get_user_meta($fifth_parent,'_mos_user_parent',true);
    $data = [
        'amount' => 0,
        'date' => date('Y-m-d'),
        'order' => $order_id,
        'from_user' => $user->ID,
        'child_gen' => 1,
        'status' => 'active',
    ];
    if($first_parent){
        $data['amount'] = $first_gen_com;
        $data['child_gen'] = 1;
        update_user_meta($first_parent,'referal_income_from_'.$order_id, $data);
        update_user_meta($first_parent,'wallet_amount', $data['amount']);
    }
    if($second_parent){
        $data['amount'] = $second_gen_com;
        $data['child_gen'] = 2;
        update_user_meta($second_parent,'referal_income_from_'.$order_id, $data);
        update_user_meta($second_parent,'wallet_amount', $data['amount']);
    }
    if($third_parent){
        $data['amount'] = $third_gen_com;
        $data['child_gen'] = 3;
        update_user_meta($third_parent,'referal_income_from_'.$order_id, $data);
        update_user_meta($third_parent,'wallet_amount', $data['amount']);
    }
    if($forth_parent){
        $data['amount'] = $forth_gen_com;
        $data['child_gen'] = 4;
        update_user_meta($forth_parent,'referal_income_from_'.$order_id, $data);
        update_user_meta($forth_parent,'wallet_amount', $data['amount']);
    }
    if($fifth_parent){
        $data['amount'] = $fifth_gen_com;
        $data['child_gen'] = 5;
        update_user_meta($fifth_parent,'referal_income_from_'.$order_id, $data);
        update_user_meta($fifth_parent,'wallet_amount', $data['amount']);
    }
}


add_action( 'woocommerce_account_transaction-table_endpoint', 'misha_my_account_endpoint_content_transaction_table' );
function misha_my_account_endpoint_content_transaction_table() {
    $msg = @$_GET['msg'];
    if ($msg == 'et3'){
        echo '<div class="woocommerce-notices-wrapper"><div class="woocommerce-message alert alert_success" role="alert"><div class="alert_icon"><i class="icon-check"></i></div><div class="alert_wrapper">Password Changed</div></div>';
    } elseif ($msg == 'et1' OR $msg == 'et2'){
        echo '<div class="woocommerce-notices-wrapper"><div class="woocommerce-message alert alert_error" role="alert"><div class="alert_icon"><i class="icon-cancel"></i></div><div class="alert_wrapper">Please try again</div></div>';
    }
    echo '<h4>Transection Password</h4>';
    echo '<form name="changepassword" id="cashoutform" action="" method="post">';
        echo '<p><label for="password">Old Password</label><input type="password" name="password" id="password" class="input" placeholde="Old Password" required></p>';
        echo '<p><label for="newpassword">New Password</label><input type="password" name="newpassword" id="newpassword" class="input" placeholde="New Password" required></p>';
        echo '<p><label for="repassword">Repeat Password</label><input type="password" name="repassword" id="repassword" class="input" placeholde="Repeat Password" required></p>';
        echo wp_nonce_field( 'mos_changepassword_action', 'mos_changepassword_field' );
        echo '<p class="submit"><button type="submit" class="button  button_size_1">Change</button></p>';
    echo '</form>';
    echo '</div>';
}
if (isset( $_POST['mos_changepassword_field'] ) && wp_verify_nonce( $_POST['mos_changepassword_field'], 'mos_changepassword_action' )) {
    $user_id = get_current_user_id();
    $transaction_code = (get_user_meta($user_id, '_mos_user_transaction_code', true))?get_user_meta($user_id, '_mos_user_transaction_code', true):'123456789';
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//    var_dump($transaction_code);
//    var_dump($_POST);
//    var_dump(remove_query_strings_split($actual_link));
    if ($_POST['newpassword'] != $_POST['repassword']) {
        $link = remove_query_strings_split($actual_link) . '?msg=et1';
    } else if ($transaction_code != $_POST['password']){
        $link = remove_query_strings_split($actual_link) . '?msg=et2';      
    } else {
        update_user_meta($user_id,'_mos_user_transaction_code',$_POST['newpassword']);
        $link = remove_query_strings_split($actual_link) . '?msg=et3';  
    }
    //var_dump($link);
    wp_redirect($link);
    exit;
/*
string(9) "123456789"
array(5) {
  ["password"]=>
  string(12) "Old Password"
  ["newpassword"]=>
  string(12) "New Password"
  ["repassword"]=>
  string(15) "Repeat Password"
  ["mos_changepassword_field"]=>
  string(10) "ed71a7d789"
  ["_wp_http_referer"]=>
  string(53) "/activecommerce/my-account/transaction-table/?error=1"
}*/
}

add_action( 'woocommerce_order_status_completed', 'mysite_woocommerce_order_status_completed', 10, 1 );

/**
*  Add custom handling fee to an order 
*/
function pt_add_handling_fee() {
    global $woocommerce;
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;
    if ($woocommerce->cart->subtotal>=2000){
        $fee = 0.00;
    } else {
        $fee = 50.00;
    }
    $title = 'Delivery Charge';
    $woocommerce->cart->add_fee( $title, $fee, TRUE, 'standard' );
}
 
// Action -> Add custom handling fee to an order
add_action( 'woocommerce_cart_calculate_fees', 'pt_add_handling_fee' );