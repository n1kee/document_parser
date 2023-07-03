<?php

require_once './APP.php';
require_once './DB.php';

$stmt = DB::connect()->prepare("SELECT * FROM AHB.goods;");
$stmt->execute();
?>
<style type="text/css">
    table {
        border-collapse: collapse;
    }
    td, th {
        border: 1px solid black;
        padding: 10px;
    }
</style>
<table>
<?php
$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

foreach($stmt->fetchAll() as $k => $row) {
    ?>
    <tr>
        <?php
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


