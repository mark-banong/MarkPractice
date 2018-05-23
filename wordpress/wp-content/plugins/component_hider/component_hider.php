<?php
/*
Plugin Name: Component hider
Author: Miller Solutions Development
Description: Remove the Save Draft and Preview in the Add New Post page.
*/

  add_action('admin_print_styles', 'remove_saveDraft_preview');
  function remove_saveDraft_preview() {

echo'<style>
  #misc-publishing-actions, #minor-publishing-actions {
  display:none;
  }
</style>';
} 

?>