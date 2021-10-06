<?php
/*
Template Name: Страница оплаты
*/



$domain = $_REQUEST['amodomain'] . '.amocrm.ru';
$domain_id = get_page_by_title($domain, 'OBJECT', 'domain')->ID;

if (isset($_GET['balance'])) {
    $output = [
        'balance' => get_field('balance', $domain_id) ? : 0,
        'users_count' => 0,
        'month_pay' => 0
    ];



    $token = get_tokens($domain, 'amomarket');

    $link = 'https://'. $domain . '/api/v4/users'; 
    $curl = curl_init(); 

    $auth = 'Authorization: ' . $token['token_type'] . ' ' . $token['access_token'];
    // echo $auth, '<br>';

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
    curl_setopt($curl, CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json', $auth]);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

    $out = curl_exec($curl);

    curl_close($curl);

    $users_count =  (int)json_decode($out, true)['_total_items'];
    $output['users_count'] = ceil($users_count / 5) * 5;

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
            $price_type = get_field('price_type');

            if ($price_type == 'month_user') {
                $output['month_pay'] += get_field('price') * $output['users_count'];
            } else if ($price_type == 'month') {
                $output['month_pay'] += get_field('price');
            }

        } 
        wp_reset_postdata();
    } 

    // echo '<pre>' . print_r($output, true) . '</pre>';
    echo json_encode($output);

} else if (isset($_GET['create_payment'])) {
    
} else if (isset($_GET['finish_payment'])) {
    
}















