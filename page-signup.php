<?php
if (isset( $_POST['custom_register_field'] ) && wp_verify_nonce( $_POST['custom_register_field'], 'custom_register_action' )) {    
    global $wpdb;  
    $err = 0;
    $emailErr = '';
    if (empty($_POST["user_login"])) {
        $emailErr = "Phone or Email Address is required";
        $err++;
    } else {
        $email = sanitize_text_field($_POST["user_login"]);
        // check if e-mail address is well-formed
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $user_name = $email;
            $user_email = $email . '@activebd4u.com';
        } else {
            $user_name = $email;
            $user_email = $email;            
        }
    }
    if (!$err){
        $user_id = username_exists( $user_name );
        if ( ! $user_id && false == email_exists( $user_email ) ) {
            $user_id = wp_create_user( $user_name, $_POST['pwd'], $user_email );
            update_user_meta($user_id,'_mos_user_referal_code',$user_id.uniqid());            
            if ($_POST['user_code']){
                $parent_id = $wpdb->get_var( "SELECT ID FROM {$wpdb->prefix}users WHERE user_login='{$_POST['user_code']}'" );
                if ($parent_id) update_user_meta($user_id,'_mos_user_parent',$parent_id);
            }
            wp_redirect( wp_login_url() );
        } else {
            $emailErr = 'User already exists.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup</title>
    <?php wp_head(); ?>
</head>
<body class="login">
    <div id="login">
        <div class="login-logo-wrapper"><a href="<?php echo home_url() ?>"><img src="http://localhost/activecommerce/wp-content/uploads/2021/02/Shoping_logo-removebg-preview.png" class="login-logo" alt=""></a></div>
        <form name="loginform" id="loginform" action="" method="post">
            <p>
                <label for="user_login">Phone or Email Address *</label>
                <input type="text" name="user_login" id="user_login" class="input" value="<?php echo @$_POST['user_login'] ?>" size="20" autocapitalize="off" required>
                <span class="error"><?php echo @$emailErr ?></span>
            </p>
            <p>
                <label for="user_code">Referral Code</label>
                <input type="text" name="user_code" id="user_code" class="input" value="<?php echo @$_POST['user_code'] ?>" size="20">
            </p>
            <p>
                <label for="user_pass">Password *</label>
                <input type="password" name="pwd" id="user_pass" class="input password-input" value="" size="20" required>
            </p>
            <p class="submit">
                <button type="submit" class="button  button_size_1">Signup</button>
            </p>
            <?php wp_nonce_field( 'custom_register_action', 'custom_register_field' ); ?>
        </form>
        <div class="text-center">Already have an account? <a href="<?php echo wp_login_url()?>">Login</a></div>
        <div class="text-center">or like to go back to <a href="<?php echo home_url()?>">Home Page</a></div>
    </div>
    <?php wp_footer(); ?>
</body>
</html>