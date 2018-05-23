$(document).ready(() => {
    $("form").on("submit", function (ev) {
        ev.preventDefault();

        var postTitle = $("[name='post_title']").val();

        alertify.confirm("Would you like to send a notification?", function () {
            alertify.alert("A notification will be sent.").set("onok", function (closeEvent) {
                //Post request
                $.post('/wp-json/sefab-api/v1/notify', {postTitle: postTitle}, function (data) {
                    console.log("data: ", data);
                });
                $("form").off("submit");
                $("#publish").click();
            });
        },
        function () {
            $("form").off("submit");
            $("#publish").click();
        }).set("closable", false);
    });
});