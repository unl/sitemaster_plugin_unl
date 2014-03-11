<div class="panel">
    <p>
        These are sites that we know are in the 4.0 UNLedu Framework.  This list is auto-generated.  We consider sites to be in 4.0 by looking at the lowest framework version on their site.
    </p>
</div>

<div>
    <ul>
        <?php
        foreach ($context->sites as $site) {
            ?>
            <li><a href="<?php echo $site->getURL() ?>"><?php  echo $site->getTitle()?></a></li>
            <?php
        }
        ?>
    </ul>
</div>