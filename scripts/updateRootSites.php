<?php
use SiteMaster\Plugins\Unl;

ini_set('display_errors', true);

//Initialize all settings and autoloaders
require_once(__DIR__ . '/../../../init.php');

$sites  = new \SiteMaster\Core\Registry\Sites\All();
$logger = new  SiteMaster\Plugins\Unl\Logger\SiteTitle(new Page());
$parser = new \Spider_Parser();
$metric = new \SiteMaster\Plugins\Unl\Metric('unl');

foreach ($sites as $site) {
    if (!$scan = $site->getLatestScan()) {
        //Don't have a scan yet, skip.
        continue;
    }

    //Download and parse the home page
    if (!$html = @file_get_contents($site->base_url)) {
        continue;
    }

    $xpath = $parser->parse($html);
    
    $attributes = \SiteMaster\Plugins\Unl\ScanAttributes::getByScansID($scan->id);
    $root = $metric->getRootSiteURL($xpath);
    if ($root && $attributes) {
        echo 'Updating root site for ' . $site->base_url . ' -- ' . $root . PHP_EOL;
        $attributes->root_site_url = $root;
        $attributes->save();
    }
    
    //Don't flood servers with requests
    sleep(1);
}
