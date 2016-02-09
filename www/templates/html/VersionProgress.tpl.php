
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
                </ul>
                <input type="submit" value="submit" />
            </form>
        </div>
    </div>
    <p>
        <a href="<?php echo \SiteMaster\Core\Config::get('URL') ?>plugins/unl/files/framework_audit.csv">Download a CSV of the full report</a> - taken <?php echo $context->getReportDate(); ?>
    </p>
</div>

<div>
    <?php if(!empty($context->getVersion())): ?>
        <?php $sites = $context->getSites(); ?>
        <h2>Results</h2>
        <?php if (0 < count($sites)): ?>
            <ul class="site-list-progress">
                <?php foreach ($sites as $base_url): ?>
                    <?php
                    if (!$site = \SiteMaster\Core\Registry\Site::getByBaseURL($base_url)) {
                        continue;
                    }
                    
                    $estimated_completion = 'none';
                    $comments = 'none';
                    
                    if ($progress = \SiteMaster\Plugins\Unl\Progress::getBySitesID($site->id)) {
                        $estimated_completion = $progress->estimated_completion;
                        $comments = $progress->self_comments;
                    }
                    
                    ?>
                    <li class="site clear-fix">
                        <div class="panel clear-fix">
                            <div class="wdn-grid-set">
                                <div class="bp2-wdn-col-one-half">
                                    <a href="<?php echo $site->getURL(); ?>">
                                        <div class="url">
                                            <?php echo $site->base_url ?>
                                        </div>
                                        <div class="title">
                                            <?php echo $site->getTitle() ?>
                                        </div>
                                    </a>
                                </div>
                                <div class="bp2-wdn-col-one-half">
                                    <dl class="progress">
                                        <dt>Estimated Completion Date</dt>
                                        <dd><?php echo $estimated_completion ?></dd>
                                        <dt>Comments</dt>
                                        <dd><?php echo $comments ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>
                No results were found
            </p>
        <?php endif; ?>
    <?php endif; ?>
</div>
