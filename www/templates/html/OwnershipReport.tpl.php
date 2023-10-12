<div class="dcf-d-flex dcf-col-gap-vw dcf-ai-center dcf-mb-4">
    <p class="dcf-m-0">This page provides a list of UNL CMS websites and their Owner, Primary Site Manager, and Secondary Site Manager.</p>
    <button id="download_csv_button" class="dcf-btn dcf-btn-primary">Download .csv</button>
</div>
<table>
    <tr>
        <th>Webaudit id</th>
        <th>UNL CMS id</th>
        <th>Owner</th>
        <th>Primary Site Manager</th>
        <th>Secondary Site Manager</th>
    </tr>
    <?php foreach ($context->sites as $site): ?>
        <?php
            $owner = "";
            $primary_site_manager = "";
            $secondary_site_manager = "";
            $site_members = $context->getMembers($site->site_id);

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
        ?>
        <tr>
            <td><?php echo $site->site_id; ?></td>
            <td><?php echo $site->unlcms_site_id; ?></td>
            <td><?php echo $owner; ?></td>
            <td><?php echo $primary_site_manager; ?></td>
            <td><?php echo $secondary_site_manager; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<script defer>
    const ownership_table = document.getElementById('ownership_report_table');
    const download_csv_button = document.getElementById('download_csv_button');
    const date = new Date();
    const csv_filename = `webaudit_ownership_report_${date.getFullYear()}_${date.getMonth()}_${date.getDate()}_${date.getHours()}_${date.getMinutes()}`;

    // Format data into array
    let table_data = [];
    ownership_table.querySelectorAll('tr').forEach((row) => {
        let row_data = [];
        row.querySelectorAll('td, th').forEach((cell) => {
            row_data.push(cell.innerHTML);
        });
        table_data.push(row_data.join(','));
    });

    // When button clicked make up a file, download link, and click the link
    download_csv_button.addEventListener('click', () => {
        // Make the CSV file and convert it to a URL object
        let blob = new Blob([table_data.join('\n')], { type: 'text/csv;charset=utf-8;' });
        let url = URL.createObjectURL(blob);

        // Make the link and set the download link to the csv file
        let link = document.createElement("a");
        link.setAttribute("href", url);
        link.setAttribute("download", csv_filename);
        link.style.visibility = 'hidden';

        // Add it to the document, click it, and remove it
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    console.log(table_data);
</script>