<?php
use SiteMaster\Plugins\Unl\VersionHistory;

ini_set('display_errors', true);

//Initialize all settings and autoloaders
require_once(__DIR__ . '/../../../init.php');

$sites = new \SiteMaster\Core\Registry\Sites\All();

$found_versions = array();
$date_of_scan = date("Y-m-d", strtotime('today midnight'));

$csv = array();

$csv[] = array(
    'date of report',
    'expected HTML version',
    'expected DEP version'
);

$helper   = new \SiteMaster\Plugins\Unl\FrameworkVersionHelper();
$versions = $helper->getVersions();
$metric   = new SiteMaster\Plugins\Unl\Metric('unl');

$csv[] = array(
    \SiteMaster\Core\Util::epochToDateTime(),
    $versions['html'][0],
    $versions['dep'][0]
);

$csv[] = array();

//Headers
$csv[] = array(
    'Site URL',
    'Site Title',
    'HTML Version Found',
    'DEP Version Found',
);

function getXPath($url)
{
    $parser = new \SiteMaster\Core\Auditor\Parser\HTML5();
    
    $html = @file_get_contents($url);
    
    //Sleep for half a second to prevent flooding
    usleep(500000);
    
    if (!$html) {
        return false;
    }
    
    return $parser->parse($html);
}

foreach ($sites as $site) {
    /**
     * @var $site \SiteMaster\Core\Registry\Site
     */
    
    $title = html_entity_decode(strip_tags($site->title));
    
    if ($site->production_status != \SiteMaster\Core\Registry\Site::PRODUCTION_STATUS_PRODUCTION) {
        //Only export sites that are in production.
        continue;
    }
    
    echo 'checking: ' . $site->base_url . PHP_EOL;
    
    $xpath = getXPath($site->base_url);
    
    if (!$xpath) {
        echo "\tunable to parse" . PHP_EOL;
        continue;
    }
    
    $dep = $metric->getDEPVersion($xpath);
    $html = $metric->getHTMLVersion($xpath);
    
    //convert null to a string for more accurate array access (php converts a null key to an empty string '')
    if (null === $dep) {
        $dep = 'null';
    }
    
    if (null === $html) {
        $html = 'null';
    }
    
    if (!isset($found_versions['dep'][$dep])) {
        $found_versions['dep'][$dep] = 0;
    }

    if (!isset($found_versions['html'][$html])) {
        $found_versions['html'][$html] = 0;
    }

    $found_versions['dep'][$dep]++;
    $found_versions['html'][$html]++;

    $csv[] = array(
        $site->base_url,
        str_replace("\n", ' ', $title),
        $html,
        $dep
    );
}

//Write CSV
$fp = fopen(__DIR__ . '/../files/framework_audit.csv', 'w');

foreach ($csv as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);

//Write DB LOG
foreach ($found_versions['html'] as $html_version=>$num_sites) {
    if ($record = VersionHistory::getByDateAndVersion($date_of_scan, VersionHistory::VERSION_TYPE_HTML, $html_version)) {
        $record->number_of_sites = $num_sites;
        $record->save();
    } else {
        VersionHistory::createHistoryRecord(VersionHistory::VERSION_TYPE_HTML, $html_version, $num_sites, $date_of_scan);
    }
}

foreach ($found_versions['dep'] as $dep_version=>$num_sites) {
    if ($record = VersionHistory::getByDateAndVersion($date_of_scan, VersionHistory::VERSION_TYPE_DEP, $dep_version)) {
        $record->number_of_sites = $num_sites;
        $record->save();
    } else {
        VersionHistory::createHistoryRecord(VersionHistory::VERSION_TYPE_DEP, $dep_version, $num_sites, $date_of_scan);
    }
}

