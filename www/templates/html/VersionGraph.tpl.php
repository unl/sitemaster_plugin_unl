
<?php
$data = $context->getData()->getRawObject();
//TODO: Show only the first of the last 12 months + last 5 days
//TODO: Only show the versions which were reported (dep)
?>
<div class="graph-container">
    <canvas id="<?php echo $context->getVersionType(); ?>_chart"></canvas>
    <div class="legend-container">
        <div id="<?php echo $context->getVersionType(); ?>_chart_legend"></div>
    </div>
    
    <script>
        require(['jquery', '<?php echo \SiteMaster\Core\Config::get('URL') . 'www/js/vendor/chart.min.js' ?>'], function($) {
            var data = {
                labels: <?php echo json_encode($data['dates']) ?>,
                datasets: []
            };

            <?php $i = 0; ?>
            <?php foreach ($data['versions'] as $version=>$version_data): ?>
            <?php $color = '#'.\SiteMaster\Plugins\Unl\VersionReport::stringToColorCode($version); ?>
            data.datasets[<?php echo $i ?>] = {
                label: "<?php echo $version ?>",
                fillColor: "<?php echo $color ?>",
                strokeColor: "<?php echo $color ?>",
                pointColor: "<?php echo $color ?>",
                pointHighlightStroke: "<?php echo $color ?>",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                data: <?php echo json_encode($version_data) ?>
            };
            <?php $i++; ?>
            <?php endforeach; ?>
            
            var ctx = document.getElementById("<?php echo $context->getVersionType(); ?>_chart").getContext("2d");
            var chart = new Chart(ctx).Line(data, {
                responsive: false,
                maintainAspectRatio: false,
                datasetFill: false,
                legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span class=\"color\" style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
                tooltipFontSize: 10,
                tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>",
                multiTooltipTemplate: "<%if (datasetLabel){%><%=datasetLabel%>: <%}%><%= value %>",
            });
    
            $("#<?php echo $context->getVersionType(); ?>_chart_legend").html(chart.generateLegend());
        });
    </script>
</div>
