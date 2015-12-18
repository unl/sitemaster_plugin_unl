<div class="panel">
    <p>
        If your site is on this list, thank you and congratulations! If a site is listed here it means that every page in the site is using the latest UNLedu Web Framework, version 4.0.
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