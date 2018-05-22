<div class="wrap">

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Add Project</h1>
            </div>

            <div class="form-area col-md-12">
                <form id='imageForm' class='addProject' action='/wp-json/sefab-api/v1/upload-image/' method='post' target='uploadIframe' enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Upload Image</label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <span class="btn btn-secondary btn-file">
                                    Browse… <input type="file" id="imgInp" name="image[]">
                                </span>
                            </span>
                            <input type="text" class="form-control" readonly>
                        </div>

                        <div id='upload-spinner' class='hidden'>   
                            <label>Uploading...</label>
                            <div class='sk-circle'>
                                <div class='sk-circle1 sk-child'></div>
                                <div class='sk-circle2 sk-child'></div>
                                <div class='sk-circle3 sk-child'></div>
                                <div class='sk-circle4 sk-child'></div>
                                <div class='sk-circle5 sk-child'></div>
                                <div class='sk-circle6 sk-child'></div>
                                <div class='sk-circle7 sk-child'></div>
                                <div class='sk-circle8 sk-child'></div>
                                <div class='sk-circle9 sk-child'></div>
                                <div class='sk-circle10 sk-child'></div>
                                <div class='sk-circle11 sk-child'></div>
                                <div class='sk-circle12 sk-child'></div>
                            </div>
                        </div>

                        <div id='upload-preview' class='thumbnail'>
                            <img id='img-upload' src='/wp-content/plugins/sefab-plugin/inc/resources/img/placeholder.png'/>
                        </div>
                    </div>
                </form>

                <iframe id="uploadIframe" name="uploadIframe" class="hidden" width="100%" height="150px"></iframe>
            </div>
        </div>

        <div class="row">
            <div class="form-area col-md-6">

                <form class="addProject" action="/wp-json/sefab-api/v1/project-add" method="POST">
                    <input type="hidden" id="imageId" name="imageId" />

                    <div class=" form-group">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name" required />
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" type="textarea" style="resize: none;" placeholder="description" name="description"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Coordinates:</label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" placeholder="Latitude" data-geo="lat" name="lat" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <input type="text" placeholder="Longitude" data-geo="lng" name="lng" class="form-control">
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary">Submit</button>
                </form>
            </div>

            <div class="map-area col-md-6">
                <input type="text" class="form-control" id="geocomplete" name="name" placeholder="Search" value="Sweden" style="margin-bottom: 10px;" required />
                <div id="map" style="width: 100%; height: 100%;"></div>
                <small>
                    Click and Drag the marker to reposition
                </small>
            </div>
        </div>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC6__29tTJIx_n24cXl125vshYEOg00jOY&callback=initMap&libraries=places" defer></script>
<script>
    var map;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: -34.397, lng: 150.644 },
            zoom: 8
        });
    }
</script>
