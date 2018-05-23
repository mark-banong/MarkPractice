<div class="wrap">
    <h1>Projects</h1>

    <a href="?page=sefab-projects&add=true" style="margin-top: 10px; margin-bottom: 10px;" class="btn btn-primary">Add Project</a>

    <table id="sefab-projects-table" class="display">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Description</th>
                <th style="text-align: center;">Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($projects as $project) { ?>
                <tr>
                    <td>
                        <?php echo $project->id; ?>
                    </td>
                    <td  class="pointer">
                        <?php echo $project->name; ?>
                    </td>
                    <td  class="pointer">
                        <?php echo $project->description; ?>
                    </td>
                    <td class="dt-body-center pointer">
                        <?php echo $project->timestamp; ?>
                    </td>
                </tr>    
            <?php } ?>
        </tbody>
    </table>
</div>

<script>
    (function ($) {
        $(document).ready(() => {
            var table = $('#sefab-projects-table').DataTable({
                order: [[3, "desc"]],
                "columnDefs": [
                    {
                        "targets": [ 0 ],
                        "visible": false,
                        "searchable": false
                    },
                ]
            });

            $('#sefab-projects-table tbody').on('click', 'tr', function () {
                var data = table.row( this ).data();
                console.log("DATA: ", data);
                window.location = window.location.pathname + "?page=sefab-projects&id=" + data[0] ;
            } );

        });
    }(jQuery));
</script>