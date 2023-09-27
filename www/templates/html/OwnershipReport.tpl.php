<p>This page provides a list of UNL CMS websites and their Owner, Primary Site Manager, and Backup Site Manager.</p>

<table>
    <tr>
        <th>Webaudit id</th>
        <th>UNL CMS id</th>
        <th>Owner</th>
        <th>Primary Site Manager</th>
        <th>Backup Site Manager</th>
    </tr>
    <?php foreach ($context->sites as $site): ?>
        <?php
            $owner = "";
            $primary_site_manager = "";
            $backup_site_manager = "";
            $site_members = $context->getMembers($site->id);

            // Get the owner, primary site manager, backup site manager from the list of members
            foreach ($site_members as $member) {
                $member_roles = $member->getRoles();
                $user = $member->getUser();
                foreach ($member_roles as $role) {
                    $role_name = $role->getRole()->role_name;
                    if ($role_name === 'Owner') {
                        $owner = $user->uid;
                    }
                    if ($role_name === 'Backup Site Manager') {
                        $backup_site_manager = $user->uid;
                    }
                    if ($role_name === 'Primary Site Manager') {
                        $primary_site_manager = $user->uid;
                    }
                }
            }
        ?>
        <tr>
            <td><?php echo $site->site_id; ?></td>
            <td><?php echo $site->unlcms_site_id; ?></td>
            <td><?php echo $owner; ?></td>
            <td><?php echo $primary_site_manager; ?></td>
            <td><?php echo $backup_site_manager; ?></td>
        </tr>
    <?php endforeach; ?>
</table>