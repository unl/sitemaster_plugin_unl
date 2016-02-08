
<?php
$version_helper = $context->getVersionHelper();
$versions = $version_helper->getAllVersions();
?>
<div class="panel">
    Select the version you want to view
    <div class="bp1-wdn-grid-set-halves">
        <div class="wdn-col">
            <form action="<?php echo $context->getURL() ?>" method="get">
                <ul>
                    <li>
                        <label for="major_release_version">
                            Select an Major Release Version
                        </label>
                        <select name="vhtml" id="major_release_version">
                            <?php foreach($versions['html'] as $version): ?>
                                <option value="<?php echo $version ?>"><?php echo $version ?></option>
                            <?php endforeach; ?>
                            <option value="none">none</option>
                        </select>
                    </li>
                    <li>
                        <label for="production_status">
                            Select a production status
                        </label>
                        <select name="production_status" id="production_status">
                            <option value="<?php echo \SiteMaster\Core\Registry\Site::PRODUCTION_STATUS_PRODUCTION?>" selected="selected">In Production</option>
                            <option value="<?php echo \SiteMaster\Core\Registry\Site::PRODUCTION_STATUS_DEVELOPMENT?>">In Development</option>
                            <option value="<?php echo \SiteMaster\Core\Registry\Site::PRODUCTION_STATUS_ARCHIVED?>">Archived</option>
                        </select>
                    </li>
                </ul>
                <input type="submit" value="submit" />
            </form>
        </div>
        <div class="wdn-col">
            <form action="<?php echo $context->getURL() ?>" method="get">
                <ul>
                    <li>
                        <label for="major_release_version">
                            Select a Minor Release Version
                        </label>
                        <select name="vdep" id="major_release_version">
                            <?php foreach($versions['dep'] as $version): ?>
                                <option value="<?php echo $version ?>"><?php echo $version ?></option>
                            <?php endforeach; ?>
                            <option value="none">none</option>
                        </select>
                    </li>
                    <li>
                        <label for="production_status">
                            Select a production status
                        </label>
                        <select name="production_status" id="production_status">
                            <option value="<?php echo \SiteMaster\Core\Registry\Site::PRODUCTION_STATUS_PRODUCTION?>" selected="selected">In Production</option>
                            <option value="<?php echo \SiteMaster\Core\Registry\Site::PRODUCTION_STATUS_DEVELOPMENT?>">In Development</option>
                            <option value="<?php echo \SiteMaster\Core\Registry\Site::PRODUCTION_STATUS_ARCHIVED?>">Archived</option>
                        </select>
                    </li>
                </ul>
                <input type="submit" value="submit" />
            </form>
        </div>
    </div>
</div>

<div>
    <?php if(!empty($context->getVersion())): ?>
        <?php $sites = $context->getSites(); ?>
        <h2>Results</h2>
        <?php if (0 < count($sites)): ?>
            <ul>
                <?php foreach ($sites as $base_url): ?>
                    <?php $site = \SiteMaster\Core\Registry\Site::getByBaseURL($base_url); ?>
                    <li><a href="<?php echo $site->getURL() ?>"><?php  echo $site->getTitle()?></a> - <?php echo $site->base_url ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>
                No results were found
            </p>
        <?php endif; ?>
    <?php endif; ?>
</div>
