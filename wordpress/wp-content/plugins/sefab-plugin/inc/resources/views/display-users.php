<br>
    <br>
    <table border='1' style='width:30%' align='center'>
    <tr>
    <th>1</th>
    <th>Surname</th>
    <th>Given Name</th>
    <th>Mobile</th>
    </tr>

<?php
    
    $row = 2;
    $surname = null;
    $warning = null;
    $color = 'pink';
    
    foreach ($validated_data as $user_info) {

        $surname    = $user_info['surname'];
        $given_name = $user_info['given-name'];
        $mobile     = $user_info['mobile'];
        $warning    = $warning . $user_info['warning'];
        echo "<tr>
         <td align='center'><b>$row</b></td>
         <td align='center'>$surname</td>
         <td align='center'>$given_name</td>
         <td align='center'>$mobile</td>
        </tr>";

        $row++;
    }
    echo '</table>';
    echo "<p align='center' style='color:red'>$warning</p>";

    if (empty($warning) === true) {
        echo "<form align='center' action='/wordpress/wp-json/sefab-api/v1/insert-users-from-file/' method='post' enctype='multipart/form-data'>";
        echo "<input type='hidden' name='surname' value='<?php echo $date; ?>'>";
        echo "<input type='submit' name='Submit' class='button button-primary button-large' value='Proceed'>";
        echo "</form>";
    }
?>