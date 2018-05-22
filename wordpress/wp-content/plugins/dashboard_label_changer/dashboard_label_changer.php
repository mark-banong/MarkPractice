<?php
/*
Plugin Name: Dashboard Label Changer
Author: Miller Solutions Development
Description: Change the labels of the admin dashboard items
*/


//Change the item labels in Post Page to 'Policy'
function revcon_change_post_label() {
    global $menu;
    global $submenu;
    $menu[5][0] = 'Policies';
    $submenu['edit.php'][5][0] = 'Policies';
    $submenu['edit.php'][10][0] = 'Add New Policy';
    $submenu['edit.php'][16][0] = 'Policy Tags';
}
function revcon_change_post_object() {
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'Policies';
    $labels->singular_name = 'Policies';
    $labels->add_new = 'Add New Policy';
    $labels->add_new_item = 'New Policy';
    $labels->edit_item = 'Edit Policy';
    $labels->new_item = 'Policy';
    $labels->view_item = 'View Policy';
    $labels->search_items = 'Search Policy';
    $labels->not_found = 'No Policy found';
    $labels->not_found_in_trash = 'No Policy found in Trash';
    $labels->all_items = 'All Policy';
    $labels->menu_name = 'Policy';
    $labels->name_admin_bar = 'Policy';
}
 
add_action( 'admin_menu', 'revcon_change_post_label' );
add_action( 'init', 'revcon_change_post_object' );


//change the labels in the dashboard

function my_text_strings( $translated_text, $text, $domain ) {
switch ( $translated_text ) {
    case 'WPForms' :
        $translated_text = __( 'Forms', 'wpforms' );
        break;
}
return $translated_text;
}
add_filter( 'gettext', 'my_text_strings', 20, 3 );
?>