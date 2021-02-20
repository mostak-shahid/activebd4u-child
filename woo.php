<?php
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
 
}
/*
 * Step 3. Content for the new page in My Account, woocommerce_account_{ENDPOINT NAME}_endpoint
 */
add_action( 'woocommerce_account_generation-table_endpoint', 'misha_my_account_endpoint_content' );
function misha_my_account_endpoint_content() {
    $html = ''; 
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

/*function mysite_woocommerce_order_status_completed( $order_id ) {
    error_log( "Order complete for order $order_id", 0 );
}
add_action( 'woocommerce_order_status_completed', 'mysite_woocommerce_order_status_completed', 10, 1 );*/