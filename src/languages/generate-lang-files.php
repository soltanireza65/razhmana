<?php

$languages = array();
foreach (glob("languages/*.php") as $filename) {
    include $filename;
    $languages[] = $lang;
}



$combined = call_user_func_array('array_merge', $languages);
ksort($combined);

foreach ($languages as $i => $lang) {
    $new_lang = array();
    foreach ($combined as $key => $value) {
        if (isset($lang[$key])) {
            $new_lang[$key] = $lang[$key];
        } else {
            $new_lang[$key] = '';
        }
    }
    $languages[$i] = $new_lang;
}


foreach ($languages as $i => $lang) {
    $filename = "languages/" . ($i+1) . ".php";
    $content = "<?php\n\n\$lang = " . var_export($lang, true) . ";\n";
    file_put_contents($filename, $content);
}


/**
$filename = 'lang.csv';
$file = fopen($filename, 'w');
global $lang;
// Open a file handle
fprintf($file, "\xEF\xBB\xBF");


// Loop through the $lang array and write data to CSV file
foreach ($lang as $key => $value) {
    // Convert the key-value pair into a string with a delimiter
    $row = array($key, $value);
    $row = implode(',', $row);
    // Write the string to the CSV file
    fwrite($file, $row . PHP_EOL);
}

// Close the file handle
fclose($file);*/

