<?php
/*
Template Name: Скрипт маркета
*/
include __DIR__ . '/classes/PermissionsWorker.php';

$template = file_get_contents(__DIR__ . '/js/core.js');

$once_scripts = '';
$always_scripts = '';

$domain = $_REQUEST['amodomain'] . '.amocrm.ru';
$email = $_REQUEST['amouser'];

$domain_post = get_page_by_title($domain, 'OBJECT', 'domain');

$domain_id = $domain_post->ID;


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



if ($query->have_posts()) {
    while ($query->have_posts()) { $query->the_post();
        if (get_field('separated')) {
            continue;
        }

        $widget_id = get_field('widget');

        $permissions_worker = new PermissionsWorker($widget_id, $domain_id);
        if ($permissions_worker->is_widget_enable()) {
            $widget_slug = get_field('slug', $widget_id);
            $once_scripts .= file_get_contents(__DIR__ . '/widgets/' . $widget_slug . '/files/assets/js/once.js');
            $once_scripts .= ';';
            $always_scripts .= file_get_contents(__DIR__ . '/widgets/' . $widget_slug . '/files/assets/js/always.js');
            $always_scripts .= ';';
        }

        
    }
    wp_reset_postdata();
} 


$template = str_replace('//once', $once_scripts, $template);
$template = str_replace('//always', $always_scripts, $template);

echo $template;











