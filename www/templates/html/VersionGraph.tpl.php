
<?php
$data = $context->getData()->getRawObject();
$id = $context->getVersionType() . uniqid();
?>
<div class="graph-container framework-chart">
    <canvas id="<?php echo $id ?>_chart"></canvas>
    <script>
        require(['jquery', '<?php echo \SiteMaster\Core\Config::get('URL') . 'www/js/vendor/chart.min.js' ?>'], function($) {
            var data = {
                labels: <?php echo json_encode($data['dates']) ?>,
                datasets: []
            };

            <?php $i = 0; ?>
            <?php foreach ($data['versions'] as $version=>$version_data): ?>
            <?php $color = \SiteMaster\Plugins\Unl\VersionGraph::stringToColorCode($version); ?>
            data.datasets[<?php echo $i ?>] = {
                label: "<?php echo $version ?>",
                fillColor: "rgba(<?php echo $color ?>,.15)",
                strokeColor: "rgba(<?php echo $color ?>,1)",
                pointColor: "rgba(<?php echo $color ?>,1)",
                pointHighlightStroke: "rgba(<?php echo $color ?>,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                data: <?php echo json_encode($version_data) ?>
            };
            <?php $i++; ?>
            <?php endforeach; ?>
            
            var ctx = document.getElementById("<?php echo $id; ?>_chart").getContext("2d");
            var chart = new Chart(ctx).Line(data, {
                responsive: false,
                maintainAspectRatio: false,
                datasetFill: true,
                legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span class=\"color\" style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
                tooltipFontSize: 10,
                tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>",
                multiTooltipTemplate: "<%if (datasetLabel){%><%=datasetLabel%>: <%}%><%= value %>",
            });
    
            //$("#<?php echo $id; ?>_chart_legend").html(chart.generateLegend());
        });
    </script>
    
    <div class="table-scroll-container">
        <div class="table-scroll">
            <table class="wdn-stretch">
                <thead>
                    <th>Date</th>
                    <?php foreach ($data['versions'] as $version=>$version_data): ?>
                        <th><?php echo $version ?> <span class="legend-color" style="background-color: rgb(<?php echo \SiteMaster\Plugins\Unl\VersionGraph::stringToColorCode($version); ?>)">&nbsp;</span></th>
                    <?php endforeach; ?>
                </thead>
                <tbody>
                    <?php foreach ($data['dates'] as $key=>$date): ?>
                        <tr>
                            <td><?php echo $date ?></td>
                            <?php foreach ($data['versions'] as $version=>$version_data): ?>
                                <td><?php echo $version_data[$key] ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
