

<div>
    <h2>Major Release History</h2>
    <?php echo $savvy->render($context->getVersionGraph('HTML')); ?>
</div>

<div>
    <h2>Minor Release History</h2>
    <?php echo $savvy->render($context->getVersionGraph('DEP')); ?>
</div>


