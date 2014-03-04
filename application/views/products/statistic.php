<div>
    <canvas id="myChart" width="900" height="600"></canvas>
</div>
<?php

if($provider_statistic_history)
{
    $labels = array();
    $labels_clean = array();
    $total_products = array();
    $total_products_with_stock = array();
    
    
    foreach ($provider_statistic_history as $r)
    {
        $labels[] = '"'.$r->created_on.'"';
        $total_products[] = $r->total_products;
        $total_products_with_stock[] = $r->total_products_with_stock;
    }
    
    $i = 1;
    $j = count($labels);

    foreach ($labels as $label)
    {
        if($i == 1 || $i == $j)
        {
            $labels_clean[] = $label;
        }
        else
        {
            $labels_clean[] = "\"\"";
        }
        
        $i++;
    }
}

?>
<script>
    $(function(){
        //Get context with jQuery - using jQuery's .get() method.
        var ctx = $("#myChart").get(0).getContext("2d");
        //This will get the first returned node in the jQuery collection.
        
        var data = {
                labels : [<?php echo implode(',', $labels_clean);?>],
                datasets : [
                        {
                                fillColor : "rgba(220,220,220,0.5)",
                                strokeColor : "rgba(220,220,220,1)",
                                pointColor : "rgba(220,220,220,1)",
                                pointStrokeColor : "#fff",
                                data : [<?php echo implode(',', $total_products);?>]
                        },
                        {
                                fillColor : "rgba(103,197,207,0.5)",
                                strokeColor : "rgba(103,197,207,1)",
                                pointColor : "rgba(103,197,207,1)",
                                pointStrokeColor : "#fff",
                                data : [<?php echo implode(',', $total_products_with_stock);?>]
                        }
                ]
        }
        
        var options = {
				
                //Boolean - If we show the scale above the chart data			
                scaleOverlay : false,

                //Boolean - If we want to override with a hard coded scale
                scaleOverride : false,

                //** Required if scaleOverride is true **
                //Number - The number of steps in a hard coded scale
                scaleSteps : null,
                //Number - The value jump in the hard coded scale
                scaleStepWidth : null,
                //Number - The scale starting value
                scaleStartValue : null,

                //String - Colour of the scale line	
                scaleLineColor : "rgba(0,0,0,.1)",

                //Number - Pixel width of the scale line	
                scaleLineWidth : 1,

                //Boolean - Whether to show labels on the scale	
                scaleShowLabels : true,

                //Interpolated JS string - can access value
                scaleLabel : "<%=value%>",

                //String - Scale label font declaration for the scale label
                scaleFontFamily : "'Arial'",

                //Number - Scale label font size in pixels	
                scaleFontSize : 12,

                //String - Scale label font weight style	
                scaleFontStyle : "normal",

                //String - Scale label font colour	
                scaleFontColor : "#666",	

                ///Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines : true,

                //String - Colour of the grid lines
                scaleGridLineColor : "rgba(0,0,0,.05)",

                //Number - Width of the grid lines
                scaleGridLineWidth : 1,	

                //Boolean - Whether the line is curved between points
                bezierCurve : true,

                //Boolean - Whether to show a dot for each point
                pointDot : true,

                //Number - Radius of each point dot in pixels
                pointDotRadius : 3,

                //Number - Pixel width of point dot stroke
                pointDotStrokeWidth : 1,

                //Boolean - Whether to show a stroke for datasets
                datasetStroke : true,

                //Number - Pixel width of dataset stroke
                datasetStrokeWidth : 2,

                //Boolean - Whether to fill the dataset with a colour
                datasetFill : true,

                //Boolean - Whether to animate the chart
                animation : true,

                //Number - Number of animation steps
                animationSteps : 60,

                //String - Animation easing effect
                animationEasing : "easeOutQuart",

                //Function - Fires when the animation is complete
                onAnimationComplete : null

        }
        
        var myNewChart = new Chart(ctx).Line(data, options);
        
        
        
        
        
    });
</script>