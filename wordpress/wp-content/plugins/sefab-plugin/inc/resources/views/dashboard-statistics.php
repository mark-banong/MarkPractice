<?php if ($_GET['form'] || isset($_GET['form'])) {?>
    <h1 style="text-align: center;margin: 30px;"><?php echo $forms[0]['wp_form_id']; ?></h1>

    <?php if (count($answer_data['policies']) === 0) {?>
        <p style="text-align: center">No data available.</p>
    <?php } else {?>
        <script>
        (function ($) {
            $(document).ready(() => {



                var table = $('#sefab-form-table').DataTable({
                    searching: false,
                    columnDefs: [
                        { orderable: false, targets: '_all' }
                    ],
                    order: [[0, "asc"]],
                    paging: false,
                    bInfo: false,
                    bFilter: true
                });

                $('#sefab-form-table tbody').on('click', 'tr', function () {
                    var data = table.row( this ).data();
                    console.log("DATA: ", data);
                    // window.location = window.location.pathname + "?page=sefab-statistics&form=" + data[0] ;
                } );

            });
        }(jQuery));
        </script>
        <div style="margin: 50px;">
            <div id="sefab-table-wrapper">
                <table id="sefab-form-table" class="display">
                    <thead>
                        <tr>
                            <th>Policy</th>
                            <?php foreach ($questions as $question) {?>
                                <th colspan="<?php echo count($question['options']); ?>" ><?php echo $question['text']; ?></th>
                            <?php }?>
                        </tr>
                        <tr>
                            <th style="text-align: left;">Title</th>
                            <?php foreach ($questions as $question) {?>
                                <?php foreach ($question['options'] as $option) {?>
                                    <th><?php echo $option['text']; ?></th>
                                <?php }?>
                            <?php }?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($answer_data['policies'] as $policy) {?>
                            <tr>
                                <td><?php echo $policy['title']; ?></td>
                                <?php foreach ($policy['data'] as $policy_answer_data) {?>
                                    <?php foreach ($policy_answer_data['count'] as $option_count) {?>
                                        <td class="dt-body-center"><?php echo $option_count; ?></td>
                                    <?php }?>
                                <?php }?>
                            </tr>
                        <?php }?>
                        <tr>
                            <td style="font-weight: bold;">TOTAL</td>
                            <?php foreach ($answer_data['total'] as $total_count) {?>
                                <?php foreach ($total_count as $count) {?>
                                    <td style="text-align: center; font-weight: bold;"><?php echo $count; ?></td>
                                <?php }?>
                            <?php }?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <?php foreach ($questions as $key => $question) {?>
            <div style="margin: 50px;" class="<?php echo ($question['type'] === 'RATING' || $question['type'] === 'RADIO' || $question['type'] === 'CHECKBOX' || $question['type'] === 'SELECT') ? 'chart-item' : ''; ?>">

                <h2><?php echo $question['text']; ?></h2>
                <?php if ($question['type'] === 'RATING' || $question['type'] === 'RADIO' || $question['type'] === 'CHECKBOX' || $question['type'] === 'SELECT') {?>

                    <div id="chart-<?php echo $key; ?>-wrapper">
                        <canvas id="chart-<?php echo $key; ?>" style="display: inherit; width: auto;"></canvas>
                    </div>

                    <script>
                        (function ($) {
                            $(document).ready(() => {

                                var generateColor = function () {
                                    var r = Math.floor(Math.random() * 255);
                                    var g = Math.floor(Math.random() * 255);
                                    var b = Math.floor(Math.random() * 255);
                                    return "rgb(" + r + "," + g + "," + b + ")";
                                };

                                var labels = " ";

                                var ctx = document.getElementById("chart-<?php echo $key; ?>");
                                var chart = new Chart(ctx, {
                                    type: 'pie',
                                    data: {
                                        labels: [
                                            <?php foreach ($question['options'] as $option) {echo '"' . $option['text'] . '"' . ', ';}?>
                                        ],
                                        datasets:
                                        [
                                            {
                                                label: "<?php echo $question['text']; ?>",
                                                backgroundColor: [<?php foreach ($question['options'] as $option) {?> generateColor().toString(), <?php }?>],
                                                data: [<?php foreach ($answer_data['total'][$key] as $count) {echo $count . ', ';}?>]
                                            }
                                        ]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio : false,
                                        title: {
                                            display: false,
                                            text: "<?php echo $question['text']; ?> Options"
                                        }
                                    }
                                });
                            });
                        }(jQuery));
                    </script>
                <?php } else if ($question['type'] === 'TEXT' || $question['type'] === 'EMAIL') {?>
                    <div width="100%">
                        <table width="100%" class="row-border" id="table-data-<?php echo $key; ?>">
                            <thead>
                                <tr>
                                    <th style="text-align: left;" width="50%">Answer</th>
                                    <th style="text-align: left;" width="50%">Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($answer_data['policies'] as $policy_data) {?>
                                    <?php foreach ($policy_data['data'][$key]['answers'] as $answer) {?>
                                        <tr>
                                            <td><?php echo $answer['value']; ?></td>
                                            <td><?php echo date('d F, Y H:i a', strtotime($answer['timestamp'])); ?></td>
                                        </tr>
                                    <?php }?>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                    <script>
                    (function ($) {
                        $(document).ready(() => {
                            var table = $('#table-data-<?php echo $key; ?>').DataTable({
                                searching: false,
                                order: [[1, 'desc']],
                                columnDefs: [
                                    { orderable: false, targets: '_all' },
                                    { bSortable: false, targets: '_all' }
                                ],
                                paging: false,
                                bInfo: false,
                                bFilter: true
                            });

                            $('#sefab-form-table tbody').on('click', 'tr', function () {
                                var data = table.row( this ).data();
                                console.log("DATA: ", data);
                                // window.location = window.location.pathname + "?page=sefab-statistics&form=" + data[0] ;
                            } );

                        });
                    }(jQuery));
                    </script>
                <?php } else if ($question['type'] === 'NAME') {?>
                    <div width="100%">
                        <table width="100%" class="row-border" id="table-data-<?php echo $key; ?>">
                            <thead>
                                <tr>
                                    <th style="text-align: left;" width="50%">First</th>
                                    <th style="text-align: left;" width="50%">Last</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($answer_data['policies'] as $policy_data) {?>
                                    <tr>
                                        <td><?php echo $policy_data['data'][$key]['answers'][0]; ?></td>
                                        <td><?php echo $policy_data['data'][$key]['answers'][1]; ?></td>
                                    </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                    <script>
                    (function ($) {
                        $(document).ready(() => {
                            var table = $('#table-data-<?php echo $key; ?>').DataTable({
                                searching: false,
                                order: [[1, 'desc']],
                                columnDefs: [
                                    { orderable: false, targets: '_all' },
                                    { bSortable: false, targets: '_all' }
                                ],
                                paging: false,
                                bInfo: false,
                                bFilter: true
                            });

                            $('#sefab-form-table tbody').on('click', 'tr', function () {
                                var data = table.row( this ).data();
                                console.log("DATA: ", data);
                                // window.location = window.location.pathname + "?page=sefab-statistics&form=" + data[0] ;
                            } );

                        });
                    }(jQuery));
                    </script>
                <?php }?>

            </div>
        <?php }?>
    <?php }?>
<?php } else if ($_GET['policy'] || isset($_GET['policy'])) {?>
    <!-- READ/UNREAD POLICY SPECIFIC -->
    <script>
    (function ($) {
        $(document).ready(function () {
            var generateColor = function () {
                var r = Math.floor(Math.random() * 255);
                var g = Math.floor(Math.random() * 255);
                var b = Math.floor(Math.random() * 255);
                return "rgb(" + r + "," + g + "," + b + ")";
            };

            var labels = " ";

            var ctx = document.getElementById("chart-policy-read-unread");
            var chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ["Read: <?php echo $data['total']['read']; ?> ", "Unread: <?php echo $data['total']['unread']; ?>"],
                    datasets:
                    [
                        {
                            label: "<?php echo $question['text']; ?>",
                            backgroundColor: [generateColor().toString(), generateColor().toString()],
                            data: [<?php echo $data['total']['read']; ?>, <?php echo $data['total']['unread']; ?>]
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio : false,
                    title: {
                        display: false,
                        text: "<?php echo $question['text']; ?> Options"
                    }
                }
            });

            var readUnreadPolicyTable = $('#policy-read-unread-table').DataTable({
                searching: true,
            });

            $("#policy-read-unread-table").on('click', 'tr', function () {
                var data = readUnreadPolicyTable.row(this).data();
            });
        });
    }(jQuery));
    </script>



    <div class="wrap">
        <h1 style="text-align: center;"><?php echo $data['policy']->title; ?></h1>

        <div id="chart-policy-read-unread-wrapper">
            <canvas id="chart-policy-read-unread" style="display: inherit; width: auto;"></canvas>
        </div>

        <table id="policy-read-unread-table" width="100%" class="display">
            <thead>
                <tr>
                    <th style="text-align: left;">User</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['userData'] as $user_data) {?>
                    <tr>
                        <td>
                            <?php echo $user_data['name']; ?>
                        </td>
                        <td class="dt-body-center">
                            <b style="color: <?php echo ($user_data['remark'] === 'Read') ? '#0cd11c' : '#ff2842'; ?>;"><?php echo $user_data['remark']; ?></b>
                        </td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
<?php } else {?>
    <!-- READ/UNREAD POLICIES -->
    <script>
    (function ($) {
        $(document).ready(() => {
            var ctx = document.getElementById("myChart");
            var chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: [ "<?php echo $read_unread_amount['read']; ?> Read", "<?php echo $read_unread_amount['unread']; ?> Unread"],
                    datasets:
                    [
                        {
                            label: "Read/Unread Policies",
                            backgroundColor: ["#3e95cd", "#cc2828"],
                            data: [<?php echo $read_unread_amount['read']; ?>, <?php echo $read_unread_amount['unread']; ?>]
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio : false,
                    title: {
                        display: true,
                        text: 'Read/Unread Policies'
                    }
                }
            });

            var readUnreadPoliciesTable = $('#sefab-read-unread-table').DataTable({
                searching: false,
                order: [[4, 'desc']],
            });

            $("#sefab-read-unread-table").on('click', 'tr', function () {
                var data = readUnreadPoliciesTable.row(this).data();
                if (data) {
                    window.location = window.location.pathname + "?page=sefab-statistics&policy=" + data[1];
                }
            });

            var formTable = $('#sefab-form-table').DataTable({
                searching: false,
            });

            $('#sefab-form-table tbody').on('click', 'tr', function () {
                var data = formTable.row( this ).data();
                window.location = window.location.pathname + "?page=sefab-statistics&form=" + data[0] ;
            });

        });
    }(jQuery));
    </script>
    <div id="chart-wrapper">
        <canvas id="myChart" class="wrap" width="80px" height="50px"></canvas>
    </div>
    <!-- READ/UNREAD POLICIES -->

    <!-- READ/UNREAD POLICIES TABLE -->
    <h2>Read / Unread Policies</h2>
    <div id="sefab-table-wrapper" class="wrap">
        <table id="sefab-read-unread-table" class="display">
            <thead>
                <tr>
                    <th style="text-align: left;">Title</th>
                    <th>Id</th>
                    <th>Read</th>
                    <th>Unread</th>
                    <th>Last Updated Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($policy_read_unread_amount as $policy_read_unread_data) {?>
                    <tr>
                        <td class="pointer"><?php echo $policy_read_unread_data['title']; ?></td>
                        <td class="dt-body-center pointer"><?php echo $policy_read_unread_data['id']; ?></td>
                        <td class="dt-body-center pointer"><?php echo $policy_read_unread_data['read']; ?></td>
                        <td class="dt-body-center pointer"><?php echo $policy_read_unread_data['unread']; ?></td>
                        <td class="dt-body-center pointer"><?php echo $policy_read_unread_data['timestampUpdated']; ?></td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    </div>

    <!-- FORM TABLE -->
    <h2>Forms</h2>

    <div id="sefab-table-wrapper" class="wrap">
        <table id="sefab-form-table" data-order='[[ 0, "asc" ]]' class="display">
            <thead>
                <tr>
                    <th style="text-align: left;" width="25%">Id</th>
                    <th style="text-align: left;" width="25%">Title</th>
                    <th style="text-align: left;" width="25%">Description</th>
                    <th width="25%">Policies</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($forms as $form) {?>
                    <tr>
                        <td class="pointer"><?php echo $form['wp_form_id']; ?></td>
                        <td class="pointer"><?php echo ($form['title'] && $form['title'] !== 'NULL') ? $form['title'] : '--'; ?></td>
                        <td class="pointer"><?php echo ($form['description'] && $form['description'] !== 'NULL') ? $form['description'] : '--'; ?></td>
                        <td class="dt-body-center pointer"><?php echo ($form['policies']) ? $form['policies'] : 0; ?></td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
    <!-- FORM TABLE -->

<?php }?>