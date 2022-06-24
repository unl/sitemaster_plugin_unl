<?php
$scan           = $context->scan;
$site           = $scan->getSite();
?>

<div class="framework-versions info-section">
    <header>
        <h3>UNLedu Framework Versions by Page</h3>
        <div class="subhead">
            This is a list of all framework versions that we found on your site.
        </div>
    </header>
    <table data-sortlist="[[1,0],[2,0]]" class="dcf-table sortable">
        <thead>
        <tr>
            <th class="path">Path</th>
            <th class="unl-html-version">UNLedu HTML Version</th>
            <th class="unl-dep-version">UNLedu Dependents Version</th>
            <th class="unl-type-version">UNLedu Template Type</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($context->all_pages as $page) {
            $attributes = \SiteMaster\Plugins\Unl\PageAttributes::getByScannedPageID($page->id);
            ?>
            <tr>
                <td class="path">
                    <a href="<?php echo $page->getURL()?>"><?php echo $theme_helper->trimBaseURL($site->base_url, $page->uri) ?></a>
                </td>
                <td class="unl-html-version">
                    <?php
                    $version = '';
                    if ($attributes) {
                        $version = $attributes->html_version;
                    }
                    echo $version;
                    ?>
                </td>
                <td class="unl-dep-version">
                    <?php
                    $version = '';
                    if ($attributes) {
                        $version = $attributes->dep_version;
                    }
                    echo $version;
                    ?>
                </td>
                <td class="unl-type-version">
                    <?php
                    $version = '';
                    if ($attributes) {
                        $version = $attributes->template_type;
                    }
                    echo $version;
                    ?>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>
</div>
