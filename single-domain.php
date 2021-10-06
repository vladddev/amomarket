<?php
// Работа с доменом (из маркетплейса) - получение активных виджетов и их регистрация
// для последующегй сборки 
include __DIR__ . '/classes/PermissionsWorker.php';


try {
    

$domain_id = get_queried_object_id();
$order = 0;


$query = new WP_Query([
    'post_type' => 'widget-order',
    'posts_per_page' => -1,
    'meta_query' => [
        'relation' => 'AND',
        [
            'key' => 'domain',
            'value' => $domain_id
        ]
    ]
]);


?>

<div id="work-area-market_core-content">
    <ul class="market-widget-list">
        <?php 
        

        if ($query->have_posts()) {
            while ($query->have_posts()) { $query->the_post();
                if (get_field('separated')) {
                    continue;
                }
        
                $widget_id = get_field('widget');

                $widget_slug = get_field('slug', $widget_id);
                $widget = get_post($widget_id);

                $permissions_worker = new PermissionsWorker($widget_id, $domain_id);
                if ($permissions_worker->is_widget_enable()) {
                    ?>

                    <li class="market-widget-item amomarketwidget" data-slug="<?php echo $widget_slug ?>">
                        <div class="market-widget-item-content">
                            <a><?php echo $widget->post_title ?></a>
                            <p><?php echo $widget_slug ?></p>
                        </div>
                        <div class="market-widget-item-controls">
                            <span class="active"><svg class="svg-icon svg-leads--unsorted-accept-dims"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#leads--unsorted-accept"></use></svg></span>
                        </div>
                    </li>

                    <?php
                } else {
                    ?>
                    <li class="market-widget-item">
                        <div class="market-widget-item-content">
                            <p><?php echo $widget->post_title ?></p>
                            <p>Дицензия закончилась</p>
                        </div>
                        <div class="market-widget-item-controls">
                            <span class="active"><svg class="svg-icon svg-leads--unsorted-accept-dims" style="fill: red"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#leads--unsorted-accept"></use></svg></span>
                        </div>
                    </li>
                <?php
                }

                
                
            }
            wp_reset_postdata();
        } else { ?>
            Нет активных виджетов
        <?php } ?>
    </ul>
</div>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/core.css">



<?php

} catch (\Throwable $th) {
    print_r($th);
}


























