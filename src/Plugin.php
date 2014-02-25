<?php
namespace SiteMaster\Plugins\Unl;

use SiteMaster\Core\Plugin\PluginInterface;
use SiteMaster\Core\Events\RoutesCompile;
use SiteMaster\Core\Events\Theme\PrependOutput;
use SiteMaster\Core\Events\Theme\RegisterStyleSheets;
use SiteMaster\Core\Util;

class Plugin extends PluginInterface
{
    /**
     * @return bool|mixed
     */
    public function onInstall()
    {
        $sql = file_get_contents($this->getRootDirectory() . "/data/database.sql");

        if (!Util::execMultiQuery($sql, true)) {
            return false;
        }

        return true;
    }

    /**
     * @return bool|mixed
     */
    public function onUninstall()
    {
        $sql = "SET FOREIGN_KEY_CHECKS = 0;
                drop table if exists unl_scan_attributes;
                drop table if exists unl_page_attributes;
                drop table if exists unl_site_progress;
                SET FOREIGN_KEY_CHECKS = 1";

        if (!Util::execMultiQuery($sql, true)) {
            return false;
        }

        return true;
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'UNL module';
    }

    /**
     * @return mixed|string
     */
    public function getDescription()
    {
        return 'This module contains functionality specific to UNL, such as the UNL WDN Metric';
    }

    /**
     * Called when the plugin is updated (a newer version exists).
     *
     * @param $previousVersion int The previous installed version
     * @return mixed
     */
    public function onUpdate($previousVersion)
    {
        return true;
    }

    /**
     * Returns the version of this plugin
     * Follow a YYYYMMDDxx syntax.
     *
     * for example 2013111801
     * would be 11/18/2013 - increment 1
     *
     * @return mixed
     */
    public function getVersion()
    {
        return true;
    }

    /**
     * Get an array of event listeners
     *
     * @return array
     */
    function getEventListeners()
    {
        $listeners = array();

        $listener = new Listener($this);

        $listeners[] = array(
            'event'    => RoutesCompile::EVENT_NAME,
            'listener' => array($listener, 'onRoutesCompile')
        );

        $listeners[] = array(
            'event'    => PrependOutput::EVENT_NAME,
            'listener' => array($listener, 'onThemePrependOutput')
        );

        $listeners[] = array(
            'event'    => RegisterStyleSheets::EVENT_NAME,
            'listener' => array($listener, 'onThemeRegisterStyleSheets')
        );

        return $listeners;
    }
}
