<?php

require_once './DB.php';

function uploadFile(string $pathname)
{

    enum ColumnTypes
    {
        case INTEGER;
        case FLOAT;
        case STRING;
        case TEXT;
    }

    $goodTypes = [
        "code" => [ ColumnTypes::STRING, false ],
        "name" => [ ColumnTypes::STRING, false ],
        "level_1" => [ ColumnTypes::STRING ],
        "level_2" => [ ColumnTypes::STRING ],
        "level_3" => [ ColumnTypes::STRING ],
        "price" => [ ColumnTypes::FLOAT ],
        "price_sp" => [ ColumnTypes::FLOAT ],
        "quantity" => [ ColumnTypes::INTEGER ],
        "properties" => [ ColumnTypes::TEXT ],
        "joint_purchase" => [ ColumnTypes::STRING ],
        "measurement" => [ ColumnTypes::STRING ],
        "picture" => [ ColumnTypes::STRING ],
        "show_on_min" => [ ColumnTypes::INTEGER ],
        "description" => [ ColumnTypes::TEXT ],
    ];

    $columnDefList = [];

    foreach ($goodTypes as $key => $columnConfig) {
        $columnType = $columnConfig[ 0 ];
        $isNullable = $columnConfig[ 1 ] ?? true;

        $columnDef = $key;

        switch ($columnType) {
            case ColumnTypes::STRING:
                $maxLength = $columnConfig[ 2 ] ?? 255;
                $columnDef .= " VARCHAR($maxLength)";
                break;

            case ColumnTypes::INTEGER:
                $columnDef .= " INTEGER";
                break;

            case ColumnTypes::FLOAT:
                $columnDef .= " FLOAT";
                break;

            case ColumnTypes::TEXT:
                $columnDef .= " TEXT";
                break;
        }

        switch ($isNullable) {
            case true:
                $columnDef .= " DEFAULT NULL";
                break;

            case false:
                $columnDef .= " NOT NULL";
                break;
        }

        $columnDefList [] = $columnDef;
    }

    $columnDefListString = implode(',', $columnDefList);

    DB::connect()->exec("USE AHB; DROP TABLE IF EXISTS goods; CREATE TABLE goods (id INT AUTO_INCREMENT NOT NULL, {$columnDefListString}, PRIMARY KEY(id));");

    $rowRegex = "";
    $delimeter = "^";
    foreach ($goodTypes as $key => $description) {

        switch ($description[0]) {
            case ColumnTypes::FLOAT:
            case ColumnTypes::INTEGER:
                $columnRegex = "[^A-Za-zА-Яа-я]*?";
                break;

            default:
                $columnRegex = ".*?";
                break;
        }
        $rowRegex .= "{$delimeter}({$columnRegex})";
        $delimeter = ";";
    }
    $rowRegex .= "(?<=[^,]),+";

    

    if (($handle = fopen($pathname, "r")) !== false) {
        $batchSql = "";
        $rowCount = 0;
        DB::beginTransaction();

        while (($line = fgets($handle)) !== false) {

            if (!$rowCount++) {
                continue;
            }

            preg_match_all("/{$rowRegex}/u", $line, $matches);

            $row = array_slice($matches, 1);

            $row = call_user_func_array('array_merge', $row);

            if (empty($row)) {
                continue;
            }

            $good = array_combine(array_keys($goodTypes), $row);

            foreach ($good as $key => $value) {
                $type = $goodTypes[ $key ][ 0 ];
                $parsedValue = $value;

                switch ($type) {
                    case ColumnTypes::INTEGER:
                    case ColumnTypes::FLOAT:
                        $value = preg_replace(["/,/", "[^0-9]"], [".", ""], $value);
                        break;
                }

                switch ($type) {
                    case ColumnTypes::INTEGER:
                        $parsedValue = is_numeric($value) ? (int) $value : null;
                        break;

                    case ColumnTypes::FLOAT:
                        $parsedValue = is_numeric($value) ? (float) $value : null;
                        break;
                }
                $good[ $key ] = $parsedValue;

            }

            $goodKeysList = implode(", ", array_keys($good));

            $placeholders = implode(", ", array_fill(0, count($good), "?"));

            $batchSql .= "INSERT INTO goods ($goodKeysList) VALUES ($placeholders);";

            $query = DB::connect()->prepare($batchSql);
            $query->execute(array_values($good));
            $batchSql = '';

            if (!($rowCount % 1000)) {
                DB::commit();
                DB::beginTransaction();
            }
        }
        DB::commit();
        fclose($handle);
    }
}
