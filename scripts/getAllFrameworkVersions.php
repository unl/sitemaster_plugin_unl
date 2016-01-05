<?php
ini_set('display_errors', true);

//Initialize all settings and autoloaders
require_once(__DIR__ . '/../../../init.php');

$client = new \Github\Client();
$paginator  = new Github\ResultPager( $client );
$result = $paginator->fetchAll($client->api('repo'), 'tags', array('unl', 'wdntemplates'));

//Build the tags array

$versions = array();
foreach ($result as $tag) {
    if (!preg_match('/^\d*\.\d*\.\d*$/', $tag['name'], $matches)) {
        continue;
    }

    if (preg_match('/^(\d*\.\d*)\.0$/', $tag['name'], $matches)) {
        $versions['html'][] = $matches[1];
    }
    
    $versions['dep'][] = $tag['name'];
}

//Add 3.0 because it is not tagged anywhere
$versions['html'][] = '3.0';

$version_helper = new \SiteMaster\Plugins\Unl\FrameworkVersionHelper();
$version_helper->setAllVersions($versions);
