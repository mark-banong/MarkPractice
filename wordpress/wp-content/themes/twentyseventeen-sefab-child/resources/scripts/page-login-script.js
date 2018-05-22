function hideAllModals() {
    (function($) {
        $('#sign-in-container').addClass('hidden');
        $('#login-button-container').addClass('hidden');
        $("#register-container").addClass('hidden');
        $("#verify-container").addClass('hidden');
    })( jQuery );
}

function showSignIn() {
    (function($) {
        // $ Works! You can test it with next line if you like
        // console.log($);
        hideAllModals();
        $('#sign-in-container').removeClass('hidden');
    })( jQuery );
}

function showWelcome () {
    (function($) {
        hideAllModals();
        $("#login-button-container").removeClass('hidden');
    })(jQuery);
}

function showRegistration() {
    (function($) {
        hideAllModals();
        $("#register-container").removeClass('hidden');
    })(jQuery);
}
