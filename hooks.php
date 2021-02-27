<?php
function add_slug_body_class( $classes ) {
    global $post;
    if ( isset( $post ) AND $post->post_type == 'page' ) {
        $classes[] = $post->post_type . '-' . $post->post_name;
    } else {
        $classes[] = $post->post_type . '-archive';
    }
    return $classes;
}
add_filter( 'body_class', 'add_slug_body_class' );

//with template 
//mosacademy_add_page('puppy-home', 'Puppy Home', 'templates/template-puppies-home.php');
//without template
mos_add_page('signup', 'Signup', 'default');
mos_add_page('test', 'Test', 'default');
function mos_add_page($page_slug, $page_title, $page_template) {
    $page = get_page_by_path( $page_slug , OBJECT );
    //var_dump($page);
    if(!$page){
        $page_details = array(
            'post_title' => $page_title,
            'post_name' => $page_slug,
            'post_date' => gmdate("Y-m-d h:i:s"),
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'page',
        );
        $page_id = wp_insert_post( $page_details );
        add_post_meta( $page_id, '_wp_page_template', $page_template );
    }
}

add_action( 'template_redirect', 'mos_redirect_post' );
function mos_redirect_post() {
    global $post;
    if ( is_user_logged_in() AND $post->post_name == 'signup') {
        global $wp_query;
        $wp_query->set_404();
        status_header( 404 );
        get_template_part( 404 ); 
        exit();
    }
}
add_action('wp_footer', 'mos_google_adsense');
function mos_google_adsense() {
    ?>
    <script data-ad-client="ca-pub-7739574086704977" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <?php 
}

add_action( 'admin_menu', 'admin_moswallet_page' );
function moswallet_page(){
	?>
	<div class="wrap">
		<h1>Wallet Box</h1>
		<?php        
        /*global $wpdb;    
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $limit = (@$_GET['limit'] AND $_GET['limit'] > 0)?$_GET['limit']:1;
        $data_start = (intval($limit)-1) * 10;
        //user_id={$user_id} AND after where
        $query = "SELECT umeta_id, user_id, meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key LIKE '%referal_income_from_%' OR meta_key LIKE '%cash_widraw%' ORDER BY umeta_id DESC LIMIT {$data_start},10";
        $data = $wpdb->get_results($query);
        if ($data) {

            echo '<table class="woocommerce-generation-table woocommerce-MyAccount-generations shop_table shop_table_responsive my_account_generations account-generations-table">';
                echo '<thead>';
                    echo '<tr>';
                        echo '<th class="woocommerce-generations-table__header"><span class="nobr">Date</span></th>';
                        echo '<th class="woocommerce-generations-table__header"><span class="nobr">User</span></th>';
                        echo '<th class="woocommerce-generations-table__header"><span class="nobr">Type</span></th>';
                        echo '<th class="woocommerce-generations-table__header"><span class="nobr">Amount</span></th>';
                        echo '<th class="woocommerce-generations-table__header"><span class="nobr">Status</span></th>';
                        echo '<th class="woocommerce-generations-table__header"><span class="nobr">Action</span></th>';
                    echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                    foreach($data as $value) {
                        $val =  maybe_unserialize($value->meta_value);
                        $user_info = get_userdata($value->user_id);
                        $type = ($val['amount']>0)?'Cashin':'Cashout';
                        echo '<tr class="woocommerce-generations-table__row woocommerce-generations-table__row--status-processing generation">';
                            echo '<td class="woocommerce-generations-table__cell woocommerce-generations-table__cell-generation-number" data-title="Date">'.$val['date'].'</td>';
                            echo '<td class="woocommerce-generations-table__cell woocommerce-generations-table__cell-generation-number" data-title="User">'.$user_info->user_login.'</td>';
                            echo '<td class="woocommerce-generations-table__cell woocommerce-generations-table__cell-generation-number" data-title="Type">'.$type.'</td>';
                            echo '<td class="woocommerce-generations-table__cell woocommerce-generations-table__cell-generation-number" data-title="Amount">'.$val['amount'].'</td>';
                            echo '<td class="woocommerce-generations-table__cell woocommerce-generations-table__cell-generation-number" data-title="Status">'.$val['status'].'</td>';
                            echo '<td class="woocommerce-generations-table__cell woocommerce-generations-table__cell-generation-number" data-title="Action">Nothing</td>';
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
        }*/
        ?>
        
    
    <form action="" method="post">
        <div class="mb-10">
           <?php echo wp_nonce_field( 'mos_wallet_datatable_action', 'mos_wallet_datatable_field' );?>
            <button type="submit" class="button button-primary">Paid</button>
        </div>
        <table id="example" class="display wp-list-table striped table-view-list wallet" style="width:100%">
            <thead>
                <tr>
                    
                </tr>
                <tr>
                    <th class="text-left"><input type="checkbox" id="checkall"> <label for="checkall">User</label></th>
                    <th class="text-left">Date</th>
                    <th class="text-left">Pay by</th>
                    <th class="text-left">Number</th>
                    <th class="text-left">Status</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th class="text-left">User</th>
                    <th class="text-left">Date</th>
                    <th class="text-left">Pay by</th>
                    <th class="text-left">Number</th>
                    <th class="text-left">Status</th>
                    <th class="no_filter text-right">Amount</th>
                </tr>
            </tfoot>
        </table>
    </form>
	</div>
	<?php
}
if (isset( $_POST['mos_wallet_datatable_field'] ) && wp_verify_nonce( $_POST['mos_wallet_datatable_field'], 'mos_wallet_datatable_action' )) {
    //var_dump($_POST);
    global $wpdb; 
    /*
array(4) {
  ["mos_wallet_datatable_field"]=>
  string(10) "85fe6946ee"
  ["_wp_http_referer"]=>
  string(49) "/activecommerce/wp-admin/admin.php?page=moswallet"
  ["example_length"]=>
  string(2) "10"
  ["umeta_id"]=>
  array(10) {
    [0]=>
    string(3) "534"
    [1]=>
    string(3) "532"
    [2]=>
    string(3) "530"
    [3]=>
    string(3) "528"
    [4]=>
    string(3) "526"
    [5]=>
    string(3) "524"
    [6]=>
    string(3) "522"
    [7]=>
    string(3) "520"
    [8]=>
    string(3) "518"
    [9]=>
    string(3) "478"
  }
}   
    */
    if(sizeof($_POST['umeta_id'])) {
        foreach($_POST['umeta_id'] as $umeta_id){
            $query = "SELECT meta_value FROM {$wpdb->prefix}usermeta WHERE umeta_id='{$umeta_id}'";
            $data = $wpdb->get_var($query);
            $arr = maybe_unserialize($data);
            $arr['status'] = 'paid';
            $json = maybe_serialize($arr);
            $wpdb->update( 
                $wpdb->prefix.'usermeta', 
                array( 
                    'meta_value' => $json,   // string
                ), 
                array( 'umeta_id' => $umeta_id ), 
                array( 
                    '%s',   // value1
                )
            );
        }
    }
}
add_action('admin_footer', 'mos_wallet_datatable', 9999);
function mos_wallet_datatable() {
    global $pagenow;
    $page = @$_GET['page'];
    if ($pagenow == 'admin.php' && $page == 'moswallet'){
        global $wpdb; 
        $arr = [];
        //$query = "SELECT umeta_id, user_id, meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key LIKE '%referal_income_from_%' OR meta_key LIKE '%cash_widraw%' ORDER BY umeta_id DESC";
        $query = "SELECT umeta_id, user_id, meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key LIKE '%cash_widraw%' ORDER BY umeta_id DESC";
        $data = $wpdb->get_results($query);
        if(sizeof($data)){
            $n = 0;
            foreach($data as $value){        
                $val =  maybe_unserialize($value->meta_value);
                $user_info = get_userdata($value->user_id);
                $type = ($val['amount']>0)?'Cashin':'Cashout';
                $arr[$n]['action'] = '';
                
                $arr[$n]['user'] = '<input name="umeta_id[]" class="datatable-checkbox" type="checkbox" value="'.$value->umeta_id.'" data-value="'.$value->umeta_id.'"> ' . $user_info->user_login;
                $arr[$n]['date'] = $val['date'];
                //$arr[$n]['type'] = $type;
                $arr[$n]['payby'] = $val['method'];
                $arr[$n]['phone'] = $val['phone'];
                $arr[$n]['status'] = $val['status'];
                $arr[$n]['amount'] = $val['amount'];
                $n++;
            }
        }       
        ?> 
    <script>
    jQuery(document).ready(function($) {
        $('#checkall').on('change', function(){            
            if($(this).is(":checked")) {
                $('.datatable-checkbox').prop('checked', true);
            } else {                
                $('.datatable-checkbox').prop('checked', false);
            }           
        });
        $('#example tfoot th:not(.no_filter)').each( function () {
            var title = $(this).text();
            $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
        });
        var information = <?php echo json_encode($arr) ?>;
        $('#example').DataTable( {
            //"ajax": JSON.stringify(<?php echo json_encode($output) ?>),
            
            data: information,
            responsive: true, 
            dom: 'lBfrtip',
            order: [[ 1, "desc" ]],
            buttons: [
                {
                    extend: "copy",
                    className: "button-secondary"
                },
                {
                    extend: "csv",
                    className: "button-secondary"
                },
                {
                    extend: "excel",
                    className: "button-secondary"
                },
                {
                    extend: "pdfHtml5",
                    className: "button-secondary"
                },
                {
                    extend: "print",
                    className: "button-secondary",
                    /*exportOptions: {
                        columns: [ 0, 1, 2, 3, 4 ]
                    },*/
                    footer: true,
                },
            ],
            columnDefs: [ {
                "targets": 0,
                "orderable": false
            }],
            columns: [
                { "data": "user" },
                { "data": "date" },
                { "data": "payby" },
                { "data": "phone" },
                { "data": "status" },                
                { "data": "amount" },                
            ],
            initComplete: function () {
                // Apply the search
                this.api().columns().every( function () {
                    var that = this;

                    $( 'input', this.footer() ).on( 'keyup change clear', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    } );
                } );
            },
            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                // Total over all pages
                total = api
                    .column( 5 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Total over this page
                pageTotal = api
                    .column( 5, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Update footer
                $( api.column( 5 ).footer() ).html(
                    //'$'+pageTotal +' ( $'+ total +' total)'
                    'Total: ' +pageTotal
                );
            }
        });
    });
    </script>
        <?php
    }
}
add_action( 'wpo_wcpdf_after_order_data', 'wpo_wcpdf_delivery_date', 10, 2 );
function wpo_wcpdf_delivery_date ($template_type, $order) {
    
    ?>
    <tr class="delivery-date">
        <th>Phone Number:</th>
        <td><?php echo $order->get_billing_phone(); ?></td>
    </tr>
    <?php
    
}