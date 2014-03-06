<?php
ini_set('display_errors', true);

//Initialize all settings and autoloaders
require_once(__DIR__ . '/../../../init.php');

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
    
    $site = \SiteMaster\Core\Registry\Site::getByBaseURL($site_info['uri']);
    if (false === $site) {
        //Site wasn't found, create it.
        $site = \SiteMaster\Core\Registry\Site::createNewSite($site_info['uri']);
    }
    
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
