<?php
global $wpdb; 
$arr = [];
$query = "SELECT umeta_id, user_id, meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key LIKE '%referal_income_from_%' OR meta_key LIKE '%cash_widraw%' ORDER BY umeta_id DESC";
$data = $wpdb->get_results($query);
if(sizeof($data)){
    $n = 0;
    foreach($data as $value){        
        $val =  maybe_unserialize($value->meta_value);
        $user_info = get_userdata($value->user_id);
        $type = ($val['amount']>0)?'Cashin':'Cashout';
        $arr[$n]['action'] = '';
        $arr[$n]['date'] = $val['date'];
        $arr[$n]['user'] = $user_info->user_login;
        $arr[$n]['type'] = $type;
        $arr[$n]['amount'] = $val['amount'];
        $arr[$n]['status'] = $val['status'];
        if($val['status'] == 'pending') $arr[$n]['action'] = '<a href="#">Paid</a>';
        $n++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup</title>
    <?php wp_head(); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
</head>
<body class="login">
    <pre>
    <?php //var_dump(json_encode($arr)) ?>
    </pre>
    
    <table id="example" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Date</th>
                <th>User</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Date</th>
                <th>User</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </tfoot>
    </table>
    
    <?php wp_footer(); ?>
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script>
    jQuery(document).ready(function($) {
        var information = <?php echo json_encode($arr) ?>;
        $('#example').DataTable( {
            //"ajax": JSON.stringify(<?php echo json_encode($output) ?>),
            
            data: information,
            //"dataType": 'json',
            "columns": [
                { "data": "date" },
                { "data": "user" },
                { "data": "type" },
                { "data": "amount" },                
                { "data": "status" },                
                { "data": "action" }
            ]
        });
    });
    </script>
</body>
</html>