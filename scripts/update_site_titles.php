<?php
ini_set('display_errors', true);

//Initialize all settings and autoloaders
require_once(__DIR__ . '/../../../init.php');

$sites  = new \SiteMaster\Core\Registry\Sites\All();
$logger = new  SiteMaster\Plugins\Unl\Logger\SiteTitle(new Page());
$parser = new \Spider_Parser();

foreach ($sites as $site) {
    if (!empty($site->title)) {
        //Don't overwrite custom site titles
        continue;
    }
    
    echo $site->base_url . PHP_EOL;
    
    //Download and parse the home page
    if (!$html = @file_get_contents($site->base_url)) {
        continue;
    }
    
    $xpath = $parser->parse($html);
    $title = $logger->getSiteTitle($xpath);
    
    if ($title) {
        echo "\t" . $title . PHP_EOL;
        $site->title = $title;
        $site->save();
    }
    
    //Don't flood servers with requests
    sleep(1);
}
