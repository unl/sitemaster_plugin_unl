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
use SiteMaster\Plugins\Unl\Progress;

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

    /**
     * @var bool|\SiteMaster\Plugins\Unl\Progress
     */
    public $progress = false;


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

        if (!$this->progress = Progress::getBySitesID($this->site->id)) {
            $this->progress = Progress::createNewProgress($this->site->id);
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
        return 'Edit UNL 5.3 Self Reported Progress';
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
        if ($post['self_progress'] < 0) {
            throw new InvalidArgumentException('Progress must be between 0 and 100', 400);
        }
        
        if ($post['self_progress'] > 100) {
            throw new InvalidArgumentException('Progress must be between 0 and 100', 400);
        }

        $this->progress->self_progress = (int)$post['self_progress'];
        
        $date = strtotime($post['estimated_completion']);
        
        $this->progress->estimated_completion = Util::epochToDateTime($date);
        $this->progress->self_comments = $post['self_comments'];

        if (!empty($post['replaced_by'])) {
            $this->progress->replaced_by = $post['replaced_by'];
        } else {
            $this->progress->replaced_by = null;
        }
        
        $this->progress->save();
        
        Controller::redirect($this->site->getURL(), new FlashBagMessage(FlashBagMessage::TYPE_SUCCESS, 'UNL progress has been updated'));
    }

    public function getEditURL()
    {
        return $this->getURL();
    }
}
