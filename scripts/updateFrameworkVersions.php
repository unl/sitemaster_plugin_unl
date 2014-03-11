<?php
ini_set('display_errors', true);

//Initialize all settings and autoloaders
require_once(__DIR__ . '/../../../init.php');

$version_helper = new \SiteMaster\Plugins\Unl\FrameworkVersionHelper();
$versions = $version_helper->grabVersions(false);
$version_helper->setCache($versions);
