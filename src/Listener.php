<?php
namespace SiteMaster\Plugins\Unl;

use SiteMaster\Core\Config;
use SiteMaster\Core\Events\Navigation\MainCompile;
use SiteMaster\Core\Events\RoutesCompile;
use SiteMaster\Core\Plugin\PluginListener;
use SiteMaster\Core\Events\Navigation\SiteCompile;
use SiteMaster\Core\Events\Theme\PrependOutput;
use SiteMaster\Core\Events\Theme\RegisterStyleSheets;
use SiteMaster\Core\User\Session;

class Listener extends PluginListener
{
    public function onRoutesCompile(RoutesCompile $event)
    {
        $event->addRoute('/^sites\/(?P<site_id>(\d*))\/unl_progress\/edit\/$/', __NAMESPACE__ . '\Progress\EditForm');
        $event->addRoute('/^unl_progress\/4x0\/$/', __NAMESPACE__ . '\Progress4x0');
    }

    /**
     * Compile primary navigation
     *
     * @param MainCompile $event
     */
    public function onNavigationMainCompile(MainCompile $event)
    {
        $event->addNavigationItem(Config::get('URL') . 'unl_progress/4x0/', 'Sites in 4.0');
    }

    /**
     * Compile sub navigation
     *
     * @param \SiteMaster\Core\Events\Navigation\SiteCompile $event
     */
    public function onNavigationSiteCompile(SiteCompile $event)
    {
        $site = $event->getSite();

        $user = Session::getCurrentUser();

        if ($user && $site->userIsVerified($user)) {
            $event->addNavigationItem($site->getURL() . 'progress/', '4.0 Progress');
        }
    }

    /**
     * @param PrependOutput $event the event to process
     */
    public function onThemePrependOutput(PrependOutput $event)
    {
        $object = $event->getObject();
        if ($object instanceof \SiteMaster\Core\Registry\Site\View && $event->getFormat() == 'html') {
            $sites_id = $object->site->id;
            $event->prependOutput(new Progress\Summary(array('sites_id' => $sites_id)));
        }
    }

    /**
     * @param RegisterStyleSheets $event
     */
    public function onThemeRegisterStyleSheets(RegisterStyleSheets $event)
    {
        $event->addStyleSheet(Config::get('URL') . 'plugins/unl/www/css/unl.css');
    }
}
