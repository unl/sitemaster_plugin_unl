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
        UNLedu 4.0 Progress Report
    </h2>
    <section class="wdn-grid-set">
        <div class="bp1-wdn-col-one-half">
            <span class="section-title">We found these framework versions:</span>
            <span class="section-help">These are lowest versions that we found on your site</span>
            <div class="wdn-grid-set dashboard-metrics">
                <div class="wdn-col-one-half">
                    <div class="visual-island <?php echo $current_html_valid ?>">
                        <span class="dashboard-value"><?php echo $current_html ?></span>
                        <span class="dashboard-metric">HTML Version</span>
                    </div>
                </div>
                <div class="wdn-col-one-half">
                    <div class="visual-island <?php echo $current_dep_valid ?>">
                        <span class="dashboard-value"><?php echo $current_dep ?></span>
                        <span class="dashboard-metric">Dependents Version</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="bp1-wdn-col-one-half progress-self-report">
            <span class="section-title">Self reported progress:</span>
            <dl>
                <dt>Estimated Completion Date</dt>
                <dd><?php echo $context->progress->estimated_completion ?></dd>
                <dt>Estimated Progress</dt>
                <dd><?php echo (empty($context->progress->self_progress))?'0':$context->progress->self_progress ?>%</dd>
                <dt>Comments</dt>
                <dd><?php echo $context->progress->self_comments ?></dd>
            </dl>
            <?php
            $user = \SiteMaster\Core\User\Session::getCurrentUser();

            if ($user && $context->site->userIsVerified($user)) {
                ?>
                <a href="<?php echo $context->site->getURL() ?>unl_progress/edit/" class="wdn-button wdn-pull-right">Edit self reported progress</a>
            <?php
            }
            ?>
        </div>
    </section>
</div>
