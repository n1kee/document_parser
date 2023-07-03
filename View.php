<?php

require_once './APP.php';
require_once './DB.php';

$stmt = DB::connect()->prepare("SELECT * FROM AHB.goods;");
$stmt->execute();
?>
<table style='border: solid 1px black;'>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
</tr>
<?php 
$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    
    foreach($stmt->fetchAll() as $k => $row) {
?>
    <tr>
        <?php 
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
                foreach($row as $cell) {
        ?>
            <td><?= $cell ?></td>
        <?php
        }
        ?>
    </tr>
<?php
}
?>
</table>


