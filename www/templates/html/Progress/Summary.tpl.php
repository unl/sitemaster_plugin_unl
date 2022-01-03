<?php
$current_html = '?';
$current_html_valid = 'invalid';
$current_dep = '?';
$current_dep_valid = 'invalid';

if ($context->scan_attributes) {
    $current_html = $context->scan_attributes->html_version;
    $current_dep = $context->scan_attributes->dep_version;
}

if ($context->htmlIsValid()) {
    $current_html_valid = 'valid';
}

if ($context->depIsValid()) {
    $current_dep_valid = 'valid';
}
?>

<div class="unl-progress-summary dashboard">
    <h2>
        UNLedu 5.3 Progress Report
    </h2>
    <section class="dcf-grid-full dcf-grid-halves@sm dcf-col-gap-vw dcf-txt-sm">
        <div class="dcf-p-2">
            <span class="section-title">We found these framework versions:</span>
            <span class="section-help">These are lowest versions that we found on your site</span>
            <div class="dcf-grid-full dcf-grid-halves@sm dcf-col-gap-vw dashboard-metrics"">
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
            </div>
        </div>
        <div class="progress-self-report dcf-p-2">
            <span class="section-title">Self reported progress:</span>
            <dl>
                <dt>Estimated Completion Date</dt>
                <dd><?php echo $context->progress->estimated_completion ?></dd>
                <dt>Estimated Progress</dt>
                <dd><?php echo (empty($context->progress->self_progress))?'0':$context->progress->self_progress ?>%</dd>
                <dt>Comments</dt>
                <dd><?php echo $context->progress->self_comments ?></dd>
            </dl>
        </div>
    </section>

    <div class="dcf-txt-center dcf-p-4">
        <?php
        if ($context->scan) {
            ?>
          <a href="<?php echo $context->scan->getURL() ?>unl/versions/" class="dcf-btn dcf-btn-secondary">See what versions we found</a>
            <?php
        }

        $user = \SiteMaster\Core\User\Session::getCurrentUser();

        if ($user && $context->site->userIsVerified($user)) {
            ?>
          <a href="<?php echo $context->site->getURL() ?>unl_progress/edit/" class="dcf-btn dcf-btn-primary dcf-float-right">Edit self reported progress</a>
            <?php
        }
        ?>
    </div>
</div>
