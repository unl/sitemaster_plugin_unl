<?php
ini_set('display_errors', true);

//Initialize all settings and autoloaders
require_once(__DIR__ . '/../../../init.php');

$sites  = new \SiteMaster\Core\Registry\Sites\WithGroup(['group_name'=>'unl']);
$logger = new  SiteMaster\Plugins\Unl\Logger\SiteTitle();
$parser = new \Spider_Parser();

/**
 * Always override the site title for the registry
 */

foreach ($sites as $site) {
    //Download and parse the home page
    if (!$html = @file_get_contents($site->base_url)) {
        continue;
    }
    
    $xpath = $parser->parse($html);
    $new_title = $logger->getSiteTitle($xpath);
    
    if ($site->title != $new_title) {
        
    }

    if ($site->title != $new_title) {
        echo $site->base_url . PHP_EOL;
        echo "\t old: " . $site->title . PHP_EOL;
        echo "\t new: " . $new_title . PHP_EOL;
        $site->title = $new_title;
        $site->save();
    }
    
    //Don't flood servers with requests
    usleep(500000);
}
