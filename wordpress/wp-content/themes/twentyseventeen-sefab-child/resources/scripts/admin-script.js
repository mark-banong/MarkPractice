(function($) {
    $(document).ready(() => {
        $userName = $("#user_login");
        $userName.addClass("bfh-phone");
        $userName.attr("data-format", "dddd-ddd-ddd", );
        $userName.attr("placeholder", "07xx-xxx-xxx", );
    });
}(jQuery));