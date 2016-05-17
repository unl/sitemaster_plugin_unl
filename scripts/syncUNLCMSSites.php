<?php
ini_set('display_errors', true);

//Initialize all settings and autoloaders
require_once(__DIR__ . '/../../../init.php');

function is_in_maintenance_mode($base_url) {
    $info = \SiteMaster\Core\Util::getHTTPInfo($base_url);
    sleep(1); //be nice to the server
    return 503 == $info['http_code'];
}

function determine_production_status($base_url) {
    if (is_in_maintenance_mode($base_url)) {
        return \SiteMaster\Core\Registry\Site::PRODUCTION_STATUS_ARCHIVED;
    }
    
    if (strpos($base_url, '://unlcms.unl.edu/') !== false) {
        //Sites that start in ://unlcms.unl.edu/ should be considered 'development' instances
        return \SiteMaster\Core\Registry\Site::PRODUCTION_STATUS_DEVELOPMENT;
    }

    return \SiteMaster\Core\Registry\Site::PRODUCTION_STATUS_PRODUCTION;
}

//Append the current time to prevent caching.
$cms_json_url = 'http://unlcms.unl.edu/admin/sites/unl/feed?time=' . time();

$cms_json = file_get_contents($cms_json_url);

if (false === $cms_json) {
    echo 'Could not retrieve the CMS data';
    exit(1);
}

$cms_sites = json_decode($cms_json, true);

if (false === $cms_sites
    || count($cms_sites) < 1) {
    echo 'JSON output from the CMS server was invalid';
    exit(1);
}

$admin_role = \SiteMaster\Core\Registry\Site\Role::getByRoleName('admin');

//Store all base URLs for later use, to make things easier
$cms_base_urls = array();

//Loop over all cms sites and create/update them and memberships
foreach ($cms_sites as $cms_site_id=>$site_info) {
    /*
     * @var $site_info array(2) {
                          ["uri"]=>
                          string(38) "http://unlcms.unl.edu/is/nuclearbunnys"
                          ["users"]=>
                          array(1) {
                            [0]=>
                            string(10) "lfrerichs1"
                          }
                        }
     */

    if (substr($site_info['uri'], -1) !== '/') {
        // Add trailing slash to uri
        $site_info['uri'] .= '/';
    }
    
    //Store the base url for later use
    $cms_base_urls[] = $site_info['uri'];
    
    //Find the site in our registry
    $site = \SiteMaster\Core\Registry\Site::getByBaseURL($site_info['uri']);
    if (false === $site) {
        //Site wasn't found, create it.
        $site = \SiteMaster\Core\Registry\Site::createNewSite($site_info['uri'], array(
            'source' => 'UNL_CMS'
        ));
    }
    
    if (empty($site->source)) {
        //Make sure that webaudit knows that it is a cms site
        $site->source = 'UNL_CMS';
        $site->save();
    }
    
    //update production status (auto-archive)
    $site->production_status = determine_production_status($site->base_url);
    $site->save();
    
    //Add members
    foreach ($site_info['users'] as $uid=>$user_info) {
        $uid = trim(strtolower($uid));
        $user = \SiteMaster\Core\User\User::getByUIDAndProvider($uid, 'UNL');
        if (!$user) {
            $user = \SiteMaster\Core\User\User::createUser($uid, 'UNL');
        }
        
        if (!$membership = $site->getMembershipForUser($user)) {
            $membership = \SiteMaster\Core\Registry\Site\Member::createMembership($user, $site, array(
                'source' => 'UNL_CMS',
            ));
        }

        if (!$role = $membership->getRole($admin_role->id)) {
            $role = \SiteMaster\Core\Registry\Site\Member\Role::createRoleForSiteMember($admin_role, $membership, array(
                'approved' => 'YES',
                'source' => 'UNL_CMS',
            ));
        }
        
        if (!$role->isApproved()) {
            $role->approve();
        }
    }
    
    //Remove members
    foreach ($site->getMembers() as $membership) {
        /**
         * @var $membership \SiteMaster\Core\Registry\Site\Member
         */
        if (!$role = $membership->getRole($admin_role->id)) {
            //Skip em (they don't have an admin role)
            continue;
        }
        
        if ($role->source != 'UNL_CMS') {
            //Role is not managed by this script
            continue;
        }
        
        $user = $membership->getUser();
        if ($user->provider != 'UNL') {
            //Not a UNL user
            continue;
        }
        
        if (!in_array(trim(strtolower($user->uid)), array_map('strtolower', array_keys($site_info['users'])))) {
            //remove em (they are no longer in the list of users)
            $role->delete();
        }
    }
}


//Clean up old unlcms development sites that no longer exist or have gone into production
//ONLY if we have a list of base URLs from the call of unlcms. we wouldn't want to delete everything if that api call fails for some reason.
if (!empty($cms_base_urls)) {
    //Loop over sites in the registry and remove the ones we need to
    //Only remove http://unlcms.unl.edu/ sites, as these are unlcms specific and development sites.
    //They should not ever move to non-unlcms servers. Production sites might, so we can't assume anything for them.
    $cms_sites = new \SiteMaster\Plugins\Unl\Sites\CMSDevSites();
    foreach ($cms_sites as $site) {
        /**
         * @var $site \SiteMaster\Core\Registry\Site
         */

        //Don't remove the base unlcms site.
        if ('http://unlcms.unl.edu/' == $site->base_url) {
            continue;
        }
        
        //If the cms currently knows about this site, we don't need to remove it.
        if (in_array($site->base_url, $cms_base_urls)) {
            continue;
        }
        
        //The CMS doesn't know about it, so we can remove it.
        echo 'Removing: ' . $site->base_url . PHP_EOL;
        $site->delete();
    }
}
