<?php

use dosamigos\chartjs\ChartJs;

/**
 * @var $label_line_chart
 * @var $count_line_chart
 */

//echo '<pre>';
//print_r($label_line_chart);
//print_r($count_line_chart); die;
?>
<div id="items_line" class="col-md-12 hide">
    <?= ChartJs::widget([
        'type' => 'line',
        'options' => [
            'id' => 'chart1',
            'height' => 250,
            'width' => 400
        ],
        'data' => [
            'labels' => $label_line_chart,
            'datasets' => [
                [
                    'label' => "Заявки",
                    'backgroundColor' => "rgba(179,181,198,0.1)",
                    'borderColor' => "#0088c7",
                    'pointBackgroundColor' => '#0088c7',
                    'pointBorderColor' => "#fff",
                    'pointHoverBackgroundColor' => "#fff",
                    'pointHoverBorderColor' => "rgba(179,181,198,1)",
                    'lineTension' => 0,
                    'data' => $count_line_chart
                ]
            ],
        ],
        'clientOptions' => [
            'scales' => [
                'yAxes' => [
                    [
                        'display' => true,
                        'ticks' => [
                            'suggestedMin' => 0,
                            'beginAtZero' => false
                        ]
                    ]
                ]
            ]
        ],
    ]);
    ?>
</div>
<div id="items_bar" class="col-md-12 hide">
    <?= ChartJs::widget([
        'type' => 'bar',
        'options' => [
            'id' => 'chart2_items',
            'height' => 250,
            'width' => 400
        ],
        'data' => [
            'labels' => $label_line_chart,
            'datasets' => [
                [
                    'label' => "Заявки",
                    'backgroundColor' => "#0088c7",
                    'borderColor' => "rgba(179,181,198,1)",
                    'pointBackgroundColor' => "rgba(179,181,198,1)",
                    'pointBorderColor' => "#fff",
                    'pointHoverBackgroundColor' => "#fff",
                    'pointHoverBorderColor' => "rgba(179,181,198,1)",
                    'data' => $count_line_chart
                ]
            ],
        ],
        'clientOptions' => [
            'scales' => [
                'yAxes' => [
                    [
                        'display' => true,
                        'ticks' => [
                            'suggestedMin' => 0,
                            'beginAtZero' => false
                        ]
                    ]
                ]
            ]
        ],
    ]);
    ?>
</div>