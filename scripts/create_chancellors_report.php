<?php
ini_set('display_errors', true);

//Initialize all settings and autoloaders
require_once(__DIR__ . '/../../../init.php');

$sites = new \SiteMaster\Core\Registry\Sites\All();

$csv = array();

//Headers
$csv[] = array('Site URL', 'In 4.0', 'Percent of Passing Pages', 'Latest Report URL');

foreach ($sites as $site) {
    /**
     * @var $site \SiteMaster\Core\Registry\Site
     */
    if (!$scan = $site->getLatestScan()) {
        //No scans found for this site... end early
        $csv[] = array($site->base_url, '-', '-', $site->getURL());
        continue;
    }
    
    $gpa = $scan->gpa;
    if (!$scan->isPassFail()) {
        $gpa = '-';
    }
    
    $total_pages = $scan->getDistinctPageCount();
    
    if ($total_pages = 0) {
        //Didn't find any pages in the scan, don't report as failing...
        $csv[] = array($site->base_url, '-', '-', $site->getURL());
        continue;
    }

    if (!$unl_scan_attributes = \SiteMaster\Plugins\Unl\ScanAttributes::getByScansID($scan->id)) {
        //No scan attributes found for this site... end early
        $csv[] = array($site->base_url, '-', $scan->gpa, $site->getURL());
        continue;
    }
    
    $in_4_0 = 'yes';
    if ($unl_scan_attributes->html_version != '4.0') {
        $in_4_0 = 'no';
    }
    
    $csv[] = array($site->base_url, $in_4_0, $gpa, $site->getURL());
}

$fp = fopen(__DIR__ . '/../files/4.0_report.csv', 'w');

foreach ($csv as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);
