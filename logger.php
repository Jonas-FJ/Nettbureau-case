<?php
function logMessage ($string) {
    date_default_timezone_set('Europe/Copenhagen');
    $log = fopen("log.log", "a") or die("Unable to open file\n");
    $log_string = date("d.m.Y H:i:s : ") . $string . "\n";
    fwrite($log, $log_string);
    fclose($log);
    echo $string . "\n";
}

?>