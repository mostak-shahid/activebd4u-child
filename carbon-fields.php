<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'crb_attach_theme_options' );
function crb_attach_theme_options() {
    /*Container::make( 'theme_options', __( 'Theme Options', 'crb' ) )
        ->add_fields( array(
            Field::make( 'text', 'crb_text', 'Text Field' ),
        ));
    */
    Container::make( 'post_meta', 'Refral Data' )
        ->where( 'post_type', '=', 'product' )
        ->add_fields( array(
            Field::make( 'text', 'mos_product_first_gen_com', __( '1st Generation Commission' ) )
                ->set_attribute( 'placeholder', '1st Generation Commission' ),
            Field::make( 'text', 'mos_product_second_gen_com', __( '2nd Generation Commission' ) )
                ->set_attribute( 'placeholder', '2nd Generation Commission' ),
            Field::make( 'text', 'mos_product_third_gen_com', __( '3rd Generation Commission' ) )
                ->set_attribute( 'placeholder', '3rd Generation Commission' ),
            Field::make( 'text', 'mos_product_forth_gen_com', __( '4th Generation Commission' ) )
                ->set_attribute( 'placeholder', '4th Generation Commission' ),
            Field::make( 'text', 'mos_product_fifth_gen_com', __( '5th Generation Commission' ) )
                ->set_attribute( 'placeholder', '5th Generation Commission' ),
        ));
    Container::make( 'user_meta', 'Additional Detals' )
        ->add_fields( array(
            // Field::make( 'text', 'mos_user_referal_code', 'Your Referral Code' ),
            Field::make( 'hidden', 'mos_user_parent', __( 'Parent ID' ) ),
            Field::make( 'text', 'mos_user_transaction_code', __( 'Transaction Code' ) ),
        ));
    
}
add_action( 'after_setup_theme', 'crb_load' );
function crb_load() {
    require_once( 'vendor/autoload.php' );
    \Carbon_Fields\Carbon_Fields::boot();
}