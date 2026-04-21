<div class="unl-progress-summary dashboard">
    <h2>
        PDF and Document Summary
    </h2>
    <section id="wae-summary" class="dcf-grid-full dcf-txt-sm dcf-mb-4">
        <div id="wae-summary-loading">
            <div class="section-title dcf-d-flex dcf-flex-row dcf-jc-center dcf-ai-center dcf-gap-3 ">
                <span>Loading Site's Document Data</span>
                <div class="dcf-progress-spinner dcf-d-inline-block"></div>
            </div>
        </div>
        <div id="wae-summary-error" class="dcf-d-none">
            <div class="section-title dcf-p-4 dcf-w-fit-content dcf-d-flex dcf-flex-row dcf-jc-center dcf-ai-center dcf-gap-3 dcf-m-auto" style="color: white; background-color: #C00; border:3px solid #900;">
                <svg class="dcf-h-6 dcf-w-6" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
                    <path fill="#fefdfa" d="M22.9 22.3l-11-22c-.2-.3-.7-.3-.9 0l-11 22c-.1.3.1.7.5.7h22c.4 0 .6-.4.4-.7zM10.8 8.1c0-.4.3-.7.8-.7.2 0 .4.1.5.2.1.1.2.3.2.5v7.7c0 .2-.1.4-.2.5-.1.1-.3.2-.5.2-.4 0-.7-.3-.8-.7V8.1zm.7 12.2c-.7 0-1.2-.5-1.2-1.2s.5-1.2 1.2-1.2 1.2.5 1.2 1.2-.5 1.2-1.2 1.2z"></path>
                </svg>
                <span>There was an error loading your sites document data</span>
            </div>
        </div>
        <div id="wae-summary-no-data" class="dcf-d-none">
            <div class="dcf-d-flex dcf-flex-col dcf-jc-center dcf-ai-center dcf-gap-3 ">
                <span class="section-title">No Data Found for this site</span>
                <span style="width: min(calc(100% - 4rem), 85ch);">
                    To ensure compliance with Title II accessibility standards, please log in to
                    <a href="https://wae.unl.edu/">wae.unl.edu</a> and run a site crawl for to
                    find all publicly accessible PDFs and documents. Once identified, these files
                    must be remediated or removed. In the case of simple text documents they could be
                    converted into web pages or web forms.
                </span>
            </div>
        </div>
        <div id="wae-summary-yes-data" class="dcf-d-none">
            <p class="section-title dcf-p-0 dcf-m-0">WAE found this data <span id="wae-date"></span>:</p>
            <span class="section-help">
                <a id="wae-more-info-link" href="https://wae.unl.edu/">View more info about this scan</a>
            </span>
            <div class="dcf-grid-full dcf-grid-halves@sm dcf-col-gap-vw dcf-mt-3 dcf-m-auto dashboard-metrics" style="width: min(calc(100% - 4rem), 85ch);">
                <div>
                    <div class="visual-island">
                        <span id="wae-pdf-count" class="dashboard-value">0</span>
                        <span class="dashboard-metric">Number of PDF Files</span>
                    </div>
                </div>
                <div>
                    <div class="visual-island">
                        <span id="wae-doc-count" class="dashboard-value">0</span>
                        <span class="dashboard-metric">Number of other documents</span>
                    </div>
                </div>
                <div>
                    <div class="visual-island valid">
                        <span id="wae-pdf-not-tagged" class="dashboard-value">0</span>
                        <span class="dashboard-metric">Number of PDFs not tagged</span>
                    </div>
                </div>
                <div>
                    <div class="visual-island valid">
                        <span id="wae-pdf-violations" class="dashboard-value">0</span>
                        <span class="dashboard-metric">Number of PDF Violations</span>
                    </div>
                </div>
            </div>
            <div class="dcf-txt-center dcf-p-4">
                <a href="<?php echo $context->site->getURL() ?>unl/documents/" class="dcf-btn dcf-btn-secondary">See what documents we found</a>
            </div>
        </div>
    </section>
</div>
<script type="module">
    window.wae_api = 'https://wae.unl.edu/api/scan/lookup';
    window.wae_base_url = '<?php echo($context->get_base_url()); ?>';
</script>
<script src="<?php echo $base_url . 'plugins/unl/www/js/wae-summary.js' ?>" type="module"></script>