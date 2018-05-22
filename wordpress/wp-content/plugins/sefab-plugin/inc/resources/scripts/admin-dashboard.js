jQuery(document).ready(function($) {
    $('label[for=url], input#url').remove();
});

jQuery(document).ready(function($) {
    $('form#your-profile > h2:first').remove(); // remove the "Personal Options" title

    $('form#your-profile tr.user-rich-editing-wrap').remove(); // remove the "Visual Editor" field

    $('form#your-profile tr.user-admin-color-wrap').remove(); // remove the "Admin Color Scheme" field

    $('form#your-profile tr.user-comment-shortcuts-wrap').remove(); // remove the "Keyboard Shortcuts" field

    $('form#your-profile tr.user-admin-bar-front-wrap').hide(); // remove the "Toolbar" field

    $('form#your-profile tr.user-syntax-highlighting-wrap').remove(); //remove the "Syntax higlighting option" field

    $('table.form-table tr.user-url-wrap').remove(); //remove the "website" field

});