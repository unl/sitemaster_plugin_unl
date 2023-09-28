<?php 
$sites_output = array();
foreach ($context->sites as $site){
    $owner = "";
    $primary_site_manager = "";
    $secondary_site_manager = "";
    $site_members = $context->getMembers($site->id);

    // Get the owner, primary site manager, Secondary site manager from the list of members
    foreach ($site_members as $member) {
        $member_roles = $member->getRoles();
        $user = $member->getUser();
        foreach ($member_roles as $role) {
            $role_name = $role->getRole()->role_name;
            if ($role_name === 'Owner') {
                $owner = $user->uid;
            }
            if ($role_name === 'Secondary Site Manager') {
                $secondary_site_manager = $user->uid;
            }
            if ($role_name === 'Primary Site Manager') {
                $primary_site_manager = $user->uid;
            }
        }
    }

    $sites_output[] = array(
        'webaudit_site_id' => $site->site_id,
        'unlcms_site_id' => isset($site->unlcms_site_id) ? $site->unlcms_site_id : null,
        'owner' => !empty($owner) ? $owner : null,
        'primary_site_manager' => !empty($primary_site_manager) ? $primary_site_manager : null,
        'secondary_site_manager' => !empty($secondary_site_manager) ? $secondary_site_manager : null,
    );
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($sites_output, JSON_PRETTY_PRINT);
