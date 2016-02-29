<?php
ini_set('display_errors', true);

//Initialize all settings and autoloaders
require_once(__DIR__ . '/../../../init.php');

if (!isset($argv[1])) {
    echo 'You must provide the path to a Google Analytics CSV export' . PHP_EOL;
    exit();
}

if (!file_exists($argv[1])) {
    echo "Unable to find mapping file" . PHP_EOL;
    exit();
}
$ga_csv_file = $argv[1];
$ga_csv_contents = file_get_contents($ga_csv_file);
$ga_csv_rows = explode("\n", $ga_csv_contents);

if (count($ga_csv_rows) < 7) {
    echo "Invalid CSV export provided" . PHP_EOL;
    exit();
}

$registry                 = new \SiteMaster\Core\Registry\Registry();
$in_section               = 'head';
$processed_column_headers = false;
$totals                   = array();
foreach ($ga_csv_rows as $row_number=>$row) {
    /**
     * [0] = url
     * [1] = pageviews
     */
    $data = str_getcsv($row, ",", '"');

    //Determine the section (separated by blank lines)
    if (empty($data[0])) {
        if ($in_section == 'head') {
            $in_section = 'body';
        } else {
            $in_section = 'footer';
        }
        continue;
    }
    
    //We only want to process the body of the csv
    if ($in_section != 'body') {
        continue;
    }
    
    //Skip column headers
    if (!$processed_column_headers) {
        $processed_column_headers = true;
        continue;
    }
    
    if ($data[0] == '(other)') {
        continue;
    }
    
    $url   = 'http://' . $data[0];
    $views = str_replace(',', '', $data[1]);

    if (!$closest_site = $registry->getClosestSite($url)) {
        continue;
    }

    if (!isset($totals[$closest_site->base_url])) {
        $totals[$closest_site->base_url] = 0;
    }
    
    $totals[$closest_site->base_url] += $views;
}

//We also want to filter out sites that are not in the current version of the framework
$version_helper = new \SiteMaster\Plugins\Unl\FrameworkVersionHelper();

//Now, parse the latest framework audit data
$audit_csv_file = __DIR__ . '/../files/framework_audit.csv';
$audit_csv_contents = file_get_contents($audit_csv_file);
$audit_csv_rows = explode("\n", $audit_csv_contents);

$new_csv = array();
foreach ($audit_csv_rows as $row_number=>$row) {
    /**
     * [0] = base_url
     * [1] = name
     * [2] = major version
     * [3] = minor version
     */
    $data = str_getcsv($row, ",", '"');
    if (!isset($totals[$data[0]])) {
        continue;
    }

    if ($version_helper->isCurrent($data[2], \SiteMaster\Plugins\Unl\FrameworkVersionHelper::VERSION_NAME_HTML)) {
        //Weed out all the sites that are in the current framework version
        continue;
    }

    //Append the view count
    $data[] = $totals[$data[0]];
    
    //Append the row to the new csv
    $new_csv[] = $data;
}

//Sort the array
usort($new_csv, function($a, $b) {
    if ($a[4] == $b[4]) {
        return 0;
    }

    return ($a[4] < $b[4]) ? 1 : -1;
});

//build csv
array_unshift($new_csv, array('Base URL', 'Total Views', 'Major version', 'Minor version', 'Total Views'));

$fp = fopen(__DIR__ . '/../files/framework_audit_ga.csv', 'w');

foreach ($new_csv as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);
