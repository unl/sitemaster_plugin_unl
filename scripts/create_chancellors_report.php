<?php
ini_set('display_errors', true);

//Initialize all settings and autoloaders
require_once(__DIR__ . '/../../../init.php');

$sites = new \SiteMaster\Core\Registry\Sites\All();

$csv = array();

//Headers
$csv[] = array('Site URL', 'In 4.0', 'Self Reported % Complete', 'Completion Date', 'Percent of Passing Pages', 'Latest Report URL');

foreach ($sites as $site) {
    /**
     * @var $site \SiteMaster\Core\Registry\Site
     */

    $in_4_0           = '-';
    $percent_complete = '-';
    $complete_date    = '-';
    $gpa              = '-';

    if ($progress = \SiteMaster\Plugins\Unl\Progress::getBySitesID($site->id)) {
        $complete_date    = $progress->estimated_completion;
        $percent_complete = $progress->self_progress;
    }
    
    if (!$scan = $site->getLatestScan()) {
        //No scans found for this site... end early
        $csv[] = array($site->base_url, '-', $percent_complete, $complete_date, '-', $site->getURL());
        continue;
    }
    
    if ($scan->isPassFail()) {
        //Include the gpa if the scan was pass/fail.  (we don't want to include non-pass/fail GPAs)
        $gpa = $scan->gpa;
    }
    
    $total_pages = $scan->getDistinctPageCount();
    
    if ($total_pages = 0) {
        //Didn't find any pages in the scan, don't report as failing...
        $csv[] = array($site->base_url, '-', $percent_complete, $complete_date, '-', $site->getURL());
        continue;
    }

    if (!$unl_scan_attributes = \SiteMaster\Plugins\Unl\ScanAttributes::getByScansID($scan->id)) {
        //No scan attributes found for this site... end early
        $csv[] = array($site->base_url, '-', $percent_complete, $complete_date, $scan->gpa, $site->getURL());
        continue;
    }
    
    $in_4_0 = 'yes';
    if ($unl_scan_attributes->html_version != '4.0') {
        $in_4_0 = 'no';
    }

    //We found everything we needed, add this site to the csv
    $csv[] = array($site->base_url, $in_4_0, $percent_complete, $complete_date, $gpa, $site->getURL());
}

$fp = fopen(__DIR__ . '/../files/4.0_report.csv', 'w');

foreach ($csv as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);
