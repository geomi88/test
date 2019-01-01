<div id="low_sales_graph_container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
<script>
Highcharts.chart('low_sales_graph_container', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Low Sales'
    },
    subtitle: {
        text: '<?php echo $search_key?>'
    },
    xAxis: {
        categories: [
            <?php echo $branches;?>
        ],
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Amount'
        }
    },
    tooltip: {
        headerFormat: '<table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0"></td>' +
            '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [{
        name: 'Sales',
        data: [<?php echo $total_sale;?>],
        color :  '#D26B7F'

    }]
});
</script>