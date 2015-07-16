<?php
ini_set('display_errors', true);

//Initialize all settings and autoloaders
require_once(__DIR__ . '/../../../init.php');

$sites = new \SiteMaster\Core\Registry\Sites\All();

$csv = array();

$csv[] = array(
    'date of report',
    'expected version'
);

$helper   = new \SiteMaster\Plugins\Unl\FrameworkVersionHelper();
$versions = $helper->getVersions();
$metric   = new SiteMaster\Plugins\Unl\Metric('unl');

$csv[] = array(
    \SiteMaster\Core\Util::epochToDateTime(),
    $versions['dep'][0]
);

$csv[] = array();

//Headers
$csv[] = array(
    'Site URL',
    'Site Title',
    'Version Found',
);

function getXPath($url)
{
    $parser = new \SiteMaster\Core\Auditor\Parser\HTML5();
    
    $html = @file_get_contents($url);
    
    //Sleep for half a second to prevent flooding
    usleep(500);
    
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
    
    if ($dep == $versions['dep'][0]) {
        //Don't report ones that are a success
        echo "\tUp to date... not logging" . PHP_EOL;
        continue;
    }

    $csv[] = array(
        $site->base_url,
        $title,
        $dep
    );
}

$fp = fopen(__DIR__ . '/../files/framework_audit.csv', 'w');

foreach ($csv as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);
