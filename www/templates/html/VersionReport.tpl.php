

<div>
    <h2>Major Release History (recent)</h2>
    <?php echo $savvy->render($context->getRecentHTMLGraph()); ?>
</div>

<div>
    <h2>Minor Release History (recent)</h2>
    <?php echo $savvy->render($context->getRecentDepGraph()); ?>
</div>

<div>
    <h2>Major Release History (weekly)</h2>
    <?php echo $savvy->render($context->getYearHTMLGraph()); ?>
</div>

<div>
    <h2>Minor Release History (weekly)</h2>
    <?php echo $savvy->render($context->getYearDepGraph()); ?>
</div>



