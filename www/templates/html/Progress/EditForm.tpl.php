<form action="<?php echo $context->getEditURL(); ?>" method="POST">
    <ol>
        <li>
            <label for="estimated_completion">Estimated Completion Date</label>
            <input id="estimated_completion" name="estimated_completion" type="date" max="2014-19-15" value="<?php echo $context->progress->estimated_completion ?>" >
        </li>
        <li>
            <label for="self_progress">Current Conversion Progress</label>
            <?php 
            $value = 0;
            if (!empty($context->progress->self_progress)) {
                $value = $context->progress->self_progress;
            }
            ?>
            <input id="self_progress" name="self_progress" type="range" min="0" max="100" step="1" value="<?php echo $value ?>">
        </li>
        <li>
            <label for="self_comments">Comments</label>
            <textarea name="self_comments" id="self_comments"><?php echo $context->progress->self_comments ?></textarea>
        </li>
    </ol>

    <input type="hidden" name="action" value="edit" />
    <button type="submit">Save</button>
</form>