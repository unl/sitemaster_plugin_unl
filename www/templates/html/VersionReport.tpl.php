
<p>
    This page provides a set of graphs that indicate the current and historical adoption of the UNLedu Web Framework across known sites. Statistics are built on a daily basis. The X axis indicates the date, and the Y indicates the number of sites in a given version.
</p>

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
