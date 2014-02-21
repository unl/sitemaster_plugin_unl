<?php
namespace SiteMaster\Plugins\Unl\Progress;

use SiteMaster\Core\AccessDeniedException;
use SiteMaster\Core\Config;
use SiteMaster\Core\Controller;
use SiteMaster\Core\FlashBagMessage;
use SiteMaster\Core\InvalidArgumentException;
use SiteMaster\Core\Registry\Site;
use SiteMaster\Core\RuntimeException;
use SiteMaster\Core\UnexpectedValueException;
use Sitemaster\Core\User\Session;
use SiteMaster\Core\Util;
use SiteMaster\Core\ViewableInterface;
use SiteMaster\Core\PostHandlerInterface;

class EditForm implements ViewableInterface, PostHandlerInterface
{
    /**
     * @var array
     */
    public $options = array();

    /**
     * @var \SiteMaster\Core\Registry\Site
     */
    public $site = false;

    /**
     * @var bool|\SiteMaster\Core\User\User
     */
    public $current_user = false;


    function __construct($options = array())
    {
        $this->options += $options;

        //Require login
        Session::requireLogin();

        //get the site
        if (!isset($this->options['site_id'])) {
            throw new InvalidArgumentException('a site id is required', 400);
        }

        if (!$this->site = Site::getByID($this->options['site_id'])) {
            throw new InvalidArgumentException('Could not find that site', 400);
        }

        $this->current_user = Session::getCurrentUser();

        if (!$this->canEdit()) {
            throw new AccessDeniedException('You do not have permission to edit this site.  You must be a verified member.', 403);
        }
    }

    /**
     * Get the url for this page
     *
     * @return bool|string
     */
    public function getURL()
    {
        return $this->site->getURL() . 'unl_progress/edit/';

    }

    /**
     * Get the title for this page
     *
     * @return string
     */
    public function getPageTitle()
    {
        return 'Edit UNL 4.0 Self Reported Progress';
    }

    /**
     * A user must be verified to edit a site's details
     *
     * @return bool
     */
    public function canEdit()
    {
        if (!$this->site) {
            return false;
        }

        if (!$this->current_user) {
            return false;
        }

        if ($this->site->userIsVerified($this->current_user)) {
            return true;
        }

        return false;
    }

    public function handlePost($get, $post, $files)
    {
        
    }

    public function getEditURL()
    {
        return $this->getURL();
    }
}
