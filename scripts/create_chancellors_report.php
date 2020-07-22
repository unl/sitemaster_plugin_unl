<?php
ini_set('display_errors', true);

//Initialize all settings and autoloaders
require_once(__DIR__ . '/../../../init.php');

$sites = new \SiteMaster\Core\Registry\Sites\WithGroup(['group_name'=>'unl']);

$csv = array();

function calcPercent($pages_with_errors, $total_pages)
{
    if ($total_pages == 0) {
        return 100;
    }
    
    $passing_pages = $total_pages - $pages_with_errors;
    
    return round(($passing_pages / $total_pages) * 100);
}

//Headers
$csv[] = array(
    'Site URL',
    'Site Title',
    'In 5.2',
    'Version Found',
    'Self Reported % Complete',
    'Est. Completion Date',
    'comments',
    'Percent of Passing Pages',
    'Total Pages',
    'Metric - wdn',
    'Metric - links',
    'Metric - pa11y',
    'Metric - html',
    'Scan Date',
    'Replaced By',
    'Root Site URL',
    'Root Site Title',
    'Latest Report URL'
);

foreach ($sites as $site) {
    /**
     * @var $site \SiteMaster\Core\Registry\Site
     */
    
    if ($site->production_status != \SiteMaster\Core\Registry\Site::PRODUCTION_STATUS_PRODUCTION) {
        //Only export sites that are in production.
        continue;
    }

    $in_5_2           = NULL;
    $percent_complete = NULL;
    $complete_date    = NULL;
    $gpa              = NULL;
    $comments         = NULL;
    $scan_date        = NULL;
    $version_found    = NULL;
    $total_pages      = NULL;
    $replaced_by      = NULL;
    $title            = html_entity_decode(strip_tags($site->title));
    $root_site_url    = NULL;
    $root_site_title  = NULL;
    $metric_framework = NULL;
    $metric_links     = NULL;
    $metric_a11y      = NULL;
    $metric_html      = NULL;
    

    if ($progress = \SiteMaster\Plugins\Unl\Progress::getBySitesID($site->id)) {
        $complete_date    = date('o-m-d', strtotime($progress->estimated_completion));
        $percent_complete = $progress->self_progress;
        $comments         = $progress->self_comments;
        if ($replaced_by_site = $progress->getReplacedBySite()) {
            $replaced_by = $replaced_by_site->base_url;
        }
    }
    
    if (!$scan = $site->getLatestScan(true)) {
        //No scans found for this site... end early
        $csv[] = array(
            $site->base_url,
            $title,
            NULL,
            $version_found,
            $percent_complete,
            $complete_date,
            $comments,
            $gpa,
            $total_pages,
            $metric_framework,
            $metric_links,
            $metric_a11y,
            $metric_html,
            $scan_date,
            $replaced_by,
            $root_site_url,
            $root_site_title,
            $site->getURL()
        );
        continue;
    }
    
    if ($scan->end_time) {
        $scan_date = date('o-m-d', strtotime($scan->end_time));
    }
    
    if ($scan->isPassFail()) {
        //Include the gpa if the scan was pass/fail.  (we don't want to include non-pass/fail GPAs)
        $gpa = $scan->gpa;
    }
    
    $total_pages = $scan->getDistinctPageCount();

    if (0 == $total_pages) {
        //Didn't find any pages in the scan, don't report as failing...
        continue;
    }
    
    $metric_framework = calcPercent($scan->getHotSpots(\SiteMaster\Core\Auditor\Metric::getByMachineName('unl_wdn')->id, -1, false)->count(), $total_pages);
    $metric_links     = calcPercent($scan->getHotSpots(\SiteMaster\Core\Auditor\Metric::getByMachineName('link_checker')->id, -1, false)->count(), $total_pages);
    $metric_html      = calcPercent($scan->getHotSpots(\SiteMaster\Core\Auditor\Metric::getByMachineName('w3c_html')->id, -1, false)->count(), $total_pages);
    $metric_a11y      = calcPercent($scan->getHotSpots(\SiteMaster\Core\Auditor\Metric::getByMachineName('pa11y')->id, -1, false)->count(), $total_pages);

    if (!$unl_scan_attributes = \SiteMaster\Plugins\Unl\ScanAttributes::getByScansID($scan->id)) {
        //No scan attributes found for this site... end early
        $csv[] = array(
            $site->base_url,
            $title,
            NULL,
            $version_found,
            $percent_complete,
            $complete_date,
            $comments,
            $scan->gpa,
            $total_pages,
            $metric_framework,
            $metric_links,
            $metric_a11y,
            $metric_html,
            $scan_date,
            $replaced_by,
            $root_site_url,
            $root_site_title,
            $site->getURL()
        );
        continue;
    }
    
    $in_5_2 = 'yes';
    $version_found = $unl_scan_attributes->html_version;
    if ($unl_scan_attributes->html_version != '5.2') {
        $in_5_2 = 'no';
    }
    
    if (filter_var($unl_scan_attributes->root_site_url, FILTER_VALIDATE_URL)) {
        $root_site_url = $unl_scan_attributes->root_site_url;

        if ($root_site = \SiteMaster\Core\Registry\Site::getByBaseURL($root_site_url)) {
            $root_site_title = $root_site->title;
        }
    }

    //We found everything we needed, add this site to the csv
    $csv[] = array(
        $site->base_url,
        $title,
        $in_5_2,
        $version_found,
        $percent_complete,
        $complete_date,
        $comments,
        $gpa,
        $total_pages,
        $metric_framework,
        $metric_links,
        $metric_a11y,
        $metric_html,
        $scan_date,
        $replaced_by,
        $root_site_url,
        $root_site_title,
        $site->getURL()
    );
}

$fp = fopen(__DIR__ . '/../files/5.2_report.csv', 'w');

foreach ($csv as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);
