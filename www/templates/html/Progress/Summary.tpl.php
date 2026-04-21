<?php
$current_html = '?';
$current_html_valid = 'invalid';
$current_dep = '?';
$current_dep_valid = 'invalid';
$current_type = '?';
$current_type_valid = 'invalid';

if ($context->scan_attributes) {
    $current_html = $context->scan_attributes->html_version ?? 'Not Found';
    $current_dep = $context->scan_attributes->dep_version ?? 'Not Found';
    $current_type = $context->scan_attributes->template_type ?? 'Not Found';
}

if ($context->htmlIsValid()) {
    $current_html_valid = 'valid';
}

if ($context->depIsValid()) {
    $current_dep_valid = 'valid';
}

if ($context->typeIsValid()) {
    $current_type_valid = 'valid';
}
?>

<div class="unl-progress-summary dashboard">
    <h2>
        UNLedu Framework Report
    </h2>
    <section class="dcf-grid-full dcf-txt-sm">
        <div class="dcf-p-2">
            <span class="section-title">We found these framework versions:</span>
            <span class="section-help">These are lowest versions that we found on your site</span>
            <div class="dcf-grid-full dcf-grid-thirds@sm dcf-col-gap-vw dcf-ml-3 dcf-mr-3 dcf-mt-3 dashboard-metrics"">
                <div>
                    <div class="visual-island <?php echo $current_html_valid ?>">
                        <span class="dashboard-value"><?php echo $current_html ?></span>
                        <span class="dashboard-metric">HTML Version</span>
                    </div>
                </div>
                <div>
                    <div class="visual-island <?php echo $current_dep_valid ?>">
                        <span class="dashboard-value"><?php echo $current_dep ?></span>
                        <span class="dashboard-metric">Dependents Version</span>
                    </div>
                </div>
                <div>
                    <div class="visual-island <?php echo $current_type_valid ?>">
                        <span class="dashboard-value"><?php echo $current_type ?></span>
                        <span class="dashboard-metric">Template Type</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="dcf-txt-center dcf-p-4">
        <?php
        if ($context->scan) {
            ?>
          <a href="<?php echo $context->scan->getURL() ?>unl/versions/" class="dcf-btn dcf-btn-secondary">See what versions we found</a>
            <?php
        }
        ?>
    </div>
</div>
