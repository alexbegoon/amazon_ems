<div>
    <div>
        <span style="background-color: rgba(220,220,220,0.5);">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span> - Total products</span>
        <br>
        <span style="background-color: rgba(103,197,207,0.5);">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span> - Total products with stock</span>
    </div>
    <br>
    <div>
        <span>Products count</span>
    </div>
    <div>
        <canvas id="Chart_both" width="1200" height="600"></canvas>
    </div>
    <div style="text-align: center;">
        <span>Date range</span>
    </div>
    <hr>
    <br>
    <div>
        <span style="background-color: rgba(220,220,220,0.5);">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span> - Total products</span>
    </div>
    <div>
        <span>Products count</span>
    </div>
    <div>
        <canvas id="Chart_total" width="1200" height="600"></canvas>
    </div>
    <div style="text-align: center;">
        <span>Date range</span>
    </div>
    <hr>
    <br>
    <div>
        <span style="background-color: rgba(103,197,207,0.5);">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span> - Total products with stock</span>
    </div>
    <div>
        <span>Products count</span>
    </div>
    <div>
        <canvas id="Chart_total_with_stock" width="1200" height="600"></canvas>
    </div>
    <div style="text-align: center;">
        <span>Date range</span>
    </div>
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
        $labels[] = '"'.$r->date_name.'"';
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
            $labels_clean[] = "\"--\"";
        }
        
        $i++;
    }
}

?>
<script>
    $(function(){
        
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
                scaleFontColor : "#111",	

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
        
        //Get context with jQuery - using jQuery's .get() method.
        var ctx_both = $("#Chart_both").get(0).getContext("2d");
        var ctx_total = $("#Chart_total").get(0).getContext("2d");
        var ctx_total_with_stock = $("#Chart_total_with_stock").get(0).getContext("2d");
        //This will get the first returned node in the jQuery collection.
        
        var data_both = {
                labels : [<?php echo implode(',', $labels);?>],
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
        
        var data_total = {
                labels : [<?php echo implode(',', $labels);?>],
                datasets : [
                        {
                                fillColor : "rgba(220,220,220,0.5)",
                                strokeColor : "rgba(220,220,220,1)",
                                pointColor : "rgba(220,220,220,1)",
                                pointStrokeColor : "#fff",
                                data : [<?php echo implode(',', $total_products);?>]
                        }
                ]
        }
        
        var data_total_with_stock = {
                labels : [<?php echo implode(',', $labels);?>],
                datasets : [
                        {
                                fillColor : "rgba(103,197,207,0.5)",
                                strokeColor : "rgba(103,197,207,1)",
                                pointColor : "rgba(103,197,207,1)",
                                pointStrokeColor : "#fff",
                                data : [<?php echo implode(',', $total_products_with_stock);?>]
                        }
                ]
        }
        
        
        
        var Chart_both              = new Chart(ctx_both).Line(data_both, options);
        var Chart_total             = new Chart(ctx_total).Line(data_total, options);
        var Chart_total_with_stock  = new Chart(ctx_total_with_stock).Line(data_total_with_stock, options);
        
        
        
        
        
    });
</script>