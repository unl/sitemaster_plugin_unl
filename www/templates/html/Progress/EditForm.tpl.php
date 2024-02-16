<style>
    .select2-input {
        color: black;
    }
</style>
<form class="dcf-form" action="<?php echo $context->getEditURL(); ?>" method="POST">
    <fieldset style="width: min(100%, 70ch);">
        <legend>Update Your Self Report</legend>
        <div class="dcf-form-group">
            <label for="estimated_completion">Estimated Completion Date</label>
            <input id="estimated_completion" name="estimated_completion" type="date" value="<?php echo $context->progress->estimated_completion ?>" >
        </div>
        <div class="dcf-form-group">
            <label for="self_progress">Current Conversion Progress</label>
            <?php 
            $value = 0;
            if (!empty($context->progress->self_progress)) {
                $value = $context->progress->self_progress;
            }
            ?>
            <script type="text/javascript">
                function updateSelfProgress(val) {
                    document.getElementById('self_progress_value').innerHTML=val;
                }
            </script>
            <input id="self_progress" name="self_progress" type="range" min="0" max="100" step="1" onchange="updateSelfProgress(this.value);" value="<?php echo $value ?>" aria-describedby="self_progress_help">
            <p id="self_progress_help" class="dcf-form-help dcf-mb-0">Current self reported progress: <span id="self_progress_value"><?php  echo $value ?></span>%</p>
        </div>
        <div class="dcf-form-group">
            <label for="self_comments">Comments</label>
            <textarea name="self_comments" id="self_comments"><?php echo $context->progress->self_comments ?></textarea>
        </div>
        <div class="dcf-form-group">
            <label for="replaced_by">This production site will be replaced by the development site at:</label>
            <select id="replaced_by" name="replaced_by" class="select2" aria-describedby="replaced_by_help">
                <option value="" <?php echo (empty($context->progress->replaced_by))?'':'selected="selected"' ?>>(none)</option>
                <?php
                    $sites = new \SiteMaster\Core\Registry\Sites\ByProductionStatus(array(
                        'production_status' => \SiteMaster\Core\Registry\Site::PRODUCTION_STATUS_DEVELOPMENT
                    ));
                    foreach ($sites as $site) {
                        $selected = '';
                        if ($context->progress->replaced_by == $site->id) {
                            $selected = 'selected="selected"';
                        }
                        echo '<option value="' . $site->id . '" ' . $selected . '>' . $site->base_url . '</option>' . PHP_EOL;
                    }
                ?>
            </select>
            <p id="replaced_by_help" class="dcf-form-help">Only sites marked as in development will appear in this list.</p>
        </div>
        <input type="hidden" name="action" value="edit" />
        <button class="dcf-btn dcf-btn-primary" type="submit">Save</button>
    </fieldset>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {
      $(document).ready(function () {
        WDN.loadCSS('<?php echo $base_url . 'plugins/unl/www/js/vendor/select2/select2.css' ?>');
        WDN.loadJS('<?php echo $base_url . 'plugins/unl/www/js/vendor/select2/select2.min.js' ?>', function () {
          $('.select2').select2();
        });
      });
    });
</script>