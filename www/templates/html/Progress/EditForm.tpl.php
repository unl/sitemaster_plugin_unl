<form action="<?php echo $context->getEditURL(); ?>" method="POST">
    <ol>
        <li>
            <label for="estimated_completion">Estimated Completion Date</label>
            <input id="estimated_completion" name="estimated_completion" type="date" value="<?php echo $context->progress->estimated_completion ?>" >
        </li>
        <li>
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
            <input id="self_progress" name="self_progress" type="range" min="0" max="100" step="1" onchange="updateSelfProgress(this.value);" value="<?php echo $value ?>">
            <br />
            current self reported progress: <span id="self_progress_value"><?php  echo $value ?></span>%
        </li>
        <li>
            <label for="self_comments">Comments</label>
            <textarea name="self_comments" id="self_comments"><?php echo $context->progress->self_comments ?></textarea>
        </li>
        <li>
            <label for="replaced_by">This production site will be replaced by the development site at (only sites marked as in development will appear in this list):</label>
            <select id="replaced_by" name="replaced_by" class="select2">
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
        </li>
    </ol>

    <input type="hidden" name="action" value="edit" />
    <button type="submit">Save</button>
</form>

<script>
    $(document).ready(function() {
        WDN.loadCSS('<?php echo $base_url . 'plugins/unl/www/js/vendor/select2/select2.css' ?>');
        WDN.loadJS('<?php echo $base_url . 'plugins/unl/www/js/vendor/select2/select2.min.js' ?>', function() {
            $('.select2').select2();
        });
    });
</script>