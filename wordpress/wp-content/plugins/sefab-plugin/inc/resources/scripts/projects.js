(function ($) {
  $(window).load(function () {

    var lat = -34.397;
    var lng = 150.644;
    
    if ($("#lat") && $("#lng")) {
      lat = parseFloat($("#lat").html());
      lng = parseFloat($("#lng").html());
    }

    $("#geocomplete").geocomplete({
      map: '#map',
      details: '.addProject',
      detailsAttribute: 'data-geo',
      markerOptions: {
        draggable: true
      }
    });

    $("#geocomplete").bind("geocode:dragged", function (event, latLng) {
      $("input[name=lat]").val(latLng.lat());
      $("input[name=lng]").val(latLng.lng());

      $("#reset").show();
    });

    $("#find").click(function () {
      $("#geocomplete").trigger("geocode");
    });


    $("#geocomplete").trigger("geocode");



    $("#deleteProjectBtn").click((ev) => {
      ev.preventDefault();

      alertify.confirm("Are you sure you want to delete this project?", function () {
        $("#deleteForm").submit();
      });
    });


    $(document).on('change', '.btn-file :file', function() {
      var input = $(this), label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
      input.trigger('fileselect', [label]);
		});

		$('.btn-file :file').on('fileselect', function(event, label) {
      var input = $(this).parents('.input-group').find(':text'),
          log = label;
      
      if( input.length ) {
        input.val(log);
      } else {
        if( log ) alert(log);
      }
	    
		});
		function readURL(input) {

      $("#upload-preview").addClass("hidden");
      $("#upload-spinner").removeClass("hidden");

      $("#imageForm").submit();
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function (e) {
            $('#img-upload').attr('src', e.target.result);
        }
        
        reader.readAsDataURL(input.files[0]);
      }
		}

		$("#imgInp").change(function(){
      readURL(this);
    }); 	
    
    var iframe = document.getElementById('uploadIframe');
    var content = $(iframe).contents().find("body");
    var json_data;

    $("#uploadIframe").on("load", function () {

      content = content.find("pre");

      json_data = $.parseJSON($(this).contents().find('pre').html());
      console.log("CONTENT: ", json_data);

      $("#imageId").val(json_data[0]['file_id']);

      $("#upload-preview").removeClass("hidden");
      $("#upload-spinner").addClass("hidden");
    });

  });
})(jQuery);