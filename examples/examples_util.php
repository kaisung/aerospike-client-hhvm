<?php

global $has_pygmentize;
exec("which pygmentize 2>&1", $output, $exit_code);
if ($exit_code > 0) {
    $has_pygmentize = false;
}
else {
    $has_pygmentize = true;
}

function colorize($str, $color, $bold = false) {
    $start = "\033[";
    if ($bold) $start .= "1;";
    else $start .= "0;";
    switch ($color) {
        case 'black':
            $start .= "30";
            break;
        case 'blue':
            $start .= "34";
            break;
        case 'green':
            $start .= "32";
            break;
        case 'red':
            $start .= "31";
            break;
        case 'gray':
            $start .= "37";
            break;
        case 'cyan':
            $start .= "30";
            break;
        case 'purple':
            $start .= "35";
            break;
        default:
            $start .= "30";
            break;
    }
    return $start. 'm'. $str. "\033[0m";
}

function success() {
    return colorize(" [✓] ", 'green', true)."\n";
}

function fail($msg) {
    return colorize(" [✗] \n".$msg, 'red', true)."\n";
}

function standard_fail($db) {
    return fail("Error [{$db->errorno()}] {$db->error()}");
}

function display_code($path, $after, $till) {
    global $has_pygmentize;

    if ($has_pygmentize) {
        // check if the file has already been pygmentized
        $cache_path = '/tmp/.'.basename($path);
        if (file_exists($path) && file_exists($cache_path) &&
            (filemtime($path) <= filemtime($cache_path))) {
            $path = $cache_path;
        } else {
            exec("pygmentize -fterminal256 -lphp -o$cache_path $path 2>&1", $o, $r);
            $path = $cache_path;
        }
    }
    $end = $till - 1;
    $len = $end - $after;
    passthru("cat -n $path 2>&1|head -n $end |tail -n $len");
    echo "\n";
}

?>
