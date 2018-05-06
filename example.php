<?php
require 'Jackrabbit.php';

$jr = new MeisamMulla\Jackrabbit('000000');

try {
    $rows = $jr->query([
        'Session' => '2018/19',
        'loc' => 'RIDGE',
        'cat1' => 'Ballet',
    ]);

    foreach ($rows as $row) {
        echo $row->name . "\n";
    }

} catch (Exception $e) {
    echo $e->getMessage();
}
