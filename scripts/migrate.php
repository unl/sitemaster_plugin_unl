<?php
ini_set('display_errors', true);

//Initialize all settings and autoloaders
require_once(__DIR__ . '/../../../init.php');

/**
 * Contains all the unique roles that have been imported so far
 * Used to reduce the number of DB requests
 *
 * role_name => role object
 *
 * @var $all_roles
 */
$all_roles = array();

/**
 * Contains all the unique users that have been imported so far
 * Used to reduce the number of DB requests
 *
 * uid => user object
 *
 * @var $all_users
 */
$all_users = array();

function getUser($uid)
{
    global $all_users;

    if (isset($all_users[$uid])) {
        return $all_users[$uid];
    }

    if (!$user = \SiteMaster\Core\User\User::getByUIDAndProvider($uid, 'UNL')) {
        echo "\tCreating User $uid " . PHP_EOL;
        $info = \SiteMaster\Plugins\Auth_Unl\Auth::getUserInfo($uid);
        if (!$user = \SiteMaster\Core\User\User::createUser($uid, 'UNL', $info)) {
            echo "\tUnable to create user $uid" . PHP_EOL;
            return false;
        }
    }

    $all_users[$uid] = $user;
    return $all_users[$uid];
}

function getRole($role_name)
{
    global $all_roles;

    if (isset($all_roles[$role_name])) {
        return $all_roles[$role_name];
    }

    if (!$role = \SiteMaster\Core\Registry\Site\Role::getByRoleName($role_name)) {
        $role = \SiteMaster\Core\Registry\Site\Role::createRole($role_name);
    }

    $all_roles[$role_name] = $role;

    return $all_roles[$role_name];
}

function migrateSite($base_url, $data)
{
    echo "Importing " . $base_url . PHP_EOL;

    if (!$site = \SiteMaster\Core\Registry\Site::getByBaseURL($base_url)) {
        //Add the site
        echo "\tAdding site " . $base_url . PHP_EOL;
        if (!$site = \SiteMaster\Core\Registry\Site::createNewSite($base_url, $data)) {
            echo "\tUnable to add site" . PHP_EOL;
            return false;
        }
    }

    if (!isset($data['members'])) {
        //No members, return early
        return true;
    }

    //Import members
    foreach ($data['members'] as $uid=>$member_data) {
        //Get the user for this UID
        if (!$user = getUser($uid)) {
            continue;
        }

        //Get the site membership for this user
        if (!$membership = \SiteMaster\Core\Registry\Site\Member::getByUserIDAndSiteID($user->id, $site->id)) {
            echo "\tCreating membership for $uid " . PHP_EOL;
            $fields = array(
                'status' => 'APPROVED',
                'source' => 'migrate_unl'
            );
            if (!$membership = \SiteMaster\Core\Registry\Site\Member::createMembership($user, $site, $fields)) {
                echo "\tUnable to create membership for $uid" . PHP_EOL;
                continue;
            }
        }

        //Set the roles
        if (!isset($member_data['roles'])) {
            continue;
        }

        foreach ($member_data['roles'] as $role_name) {
            $role = getRole($role_name);
            if (!$membership_role = \SiteMaster\Core\Registry\Site\Member\Role::getByRoleIDANDMembershipID($role->id, $membership->id)) {
                echo "\tCreating role for $uid and $role_name " . $uid . PHP_EOL;
                if (!$membership_role = \SiteMaster\Core\Registry\Site\Member\Role::createRoleForSiteMember($role, $membership)) {
                    echo "\tUnable to create membership role for $uid and $role_name" . PHP_EOL;
                    continue;
                }
            }
        }
    }

    return true;
}

if (!$data = file_get_contents('http://www1.unl.edu/wdn/registry/?output=json&u=*')) {
    echo "Error: unable to get data"; exit();
}

if (!$json = json_decode($data, true)) {
    echo "Error: unable to json_decode data"; exit();
}

$failed_urls = array();
foreach($json as $base_url=>$data) {
    if (substr($base_url, -1) != '/') {
        $failed_urls[$base_url] = "invalid base url";
        continue;
    }

    if (!migrateSite($base_url, $data)) {
        $failed_urls[$base_url] = "DB error creating new site";
    }
}

echo "Finished" . PHP_EOL;

if (!empty($failed_urls)) {
    echo "WARNING: FAILED TO IMPORT " . count($failed_urls) . " Sites" . PHP_EOL . PHP_EOL;
    foreach ($failed_urls as $base_url=>$reason) {
        echo $base_url . PHP_EOL;
        echo "\t" . $reason . PHP_EOL;
    }
}
