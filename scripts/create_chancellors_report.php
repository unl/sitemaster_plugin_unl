<?php
ini_set('display_errors', true);

//Initialize all settings and autoloaders
require_once(__DIR__ . '/../../../init.php');

$sites = new \SiteMaster\Core\Registry\Sites\All();

$csv = array();

//Headers
$csv[] = array('Site URL', 'Site Title', 'In 4.0', 'Version Found', 'Self Reported % Complete', 'Est. Completion Date', 'comments', 'Percent of Passing Pages', 'total_pages', 'Scan Date', 'Latest Report URL');

foreach ($sites as $site) {
    /**
     * @var $site \SiteMaster\Core\Registry\Site
     */

    $in_4_0           = '-';
    $percent_complete = NULL;
    $complete_date    = '-';
    $gpa              = NULL;
    $comments         = NULL;
    $scan_date        = NULL;
    $version_found    = NULL;
    $total_pages      = NULL;

    if ($progress = \SiteMaster\Plugins\Unl\Progress::getBySitesID($site->id)) {
        $complete_date    = $progress->estimated_completion;
        $percent_complete = $progress->self_progress;
        $comments         = $progress->self_comments;
    }
    
    if (!$scan = $site->getLatestScan(true)) {
        //No scans found for this site... end early
        $csv[] = array($site->base_url, $site->title, '-', $version_found, $percent_complete, $complete_date, $comments, $gpa, $total_pages, $scan_date, $site->getURL());
        continue;
    }
    
    if ($scan->end_time) {
        $scan_date = date('M-d-Y', strtotime($scan->end_time));
    }
    
    if ($scan->isPassFail()) {
        //Include the gpa if the scan was pass/fail.  (we don't want to include non-pass/fail GPAs)
        $gpa = $scan->gpa;
    }
    
    $total_pages = $scan->getDistinctPageCount();
    
    if (0 == $total_pages) {
        //Didn't find any pages in the scan, don't report as failing...
        $csv[] = array($site->base_url, $site->title, '-', $version_found, $percent_complete, $complete_date, $comments, $gpa, $total_pages, $scan_date, $site->getURL());
        continue;
    }

    if (!$unl_scan_attributes = \SiteMaster\Plugins\Unl\ScanAttributes::getByScansID($scan->id)) {
        //No scan attributes found for this site... end early
        $csv[] = array($site->base_url, $site->title, '-', $version_found, $percent_complete, $complete_date, $comments, $scan->gpa, $total_pages, $scan_date, $site->getURL());
        continue;
    }
    
    $in_4_0 = 'yes';
    $version_found = $unl_scan_attributes->html_version;
    if ($unl_scan_attributes->html_version != '4.0') {
        $in_4_0 = 'no';
    }

    //We found everything we needed, add this site to the csv
    $csv[] = array($site->base_url, $site->title, $in_4_0, $version_found, $percent_complete, $complete_date, $comments, $gpa, $total_pages, $scan_date, $site->getURL());
}

$fp = fopen(__DIR__ . '/../files/4.0_report.csv', 'w');

foreach ($csv as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);
