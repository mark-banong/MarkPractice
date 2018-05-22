<div class="wrap">
    <div class="container">
        <div class="row">
            <div class="col-md-10">
                <h2>Project Details</h2>
            </div>
            <div class="col-md-2">
                <form id="deleteForm" action="/wp-json/sefab-api/v1/project-delete" method="POST">
                    <input type="hidden" value="<?php echo $project->id; ?>" name="id" />

                    <button id="deleteProjectBtn" type="submit" class="btn btn-danger float-right">
                        Delete Project
                    </button>
                </form>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div id='upload-preview' class='thumbnail' style='margin-bottom: 10px;'>
                    <img id='img-upload' src='<?php echo ($project->image) ? $project->image->url : "/wp-content/plugins/sefab-plugin/inc/resources/img/placeholder.png"; ?>'/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Name:</h5>
                        <label><?php echo $project->name; ?></label>
                    </div>
                    <div class="col-md-6">
                        <h5>Timestamp</h5>
                        <label><?php echo $project->timestamp; ?></label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h5>Latitude:</h5>
                        <label id="lng"><?php echo $project->coordinates->latitude; ?></label>
                    </div>
                    <div class="col-md-6">
                        <h5>Longitude:</h5>
                        <label id="lat"><?php echo $project->coordinates->longitude; ?></label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h5>Description:</h5>
                        <label><?php echo $project->description; ?></label>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div id="map" style="width: 100%; height: 500px;"></div>
            </div>
        </div>

        

    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC6__29tTJIx_n24cXl125vshYEOg00jOY&callback=initMap&libraries=places" defer></script>
<script>
    var map;
    var latlng = { lat: <?php echo $project->coordinates->latitude; ?>, lng: <?php echo $project->coordinates->longitude; ?> };

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: latlng,
            zoom: 15
        });

        var marker = new google.maps.Marker({
          position: latlng,
          map: map,
          title: '<?php echo $project->name; ?>'
        });
    }
</script>
