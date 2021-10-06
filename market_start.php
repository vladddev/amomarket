<?php
/*
Template Name: Старт виджета 
*/



if (!isset($_REQUEST['referrer']) || !isset($_REQUEST['client_id']) || !isset($_REQUEST['client_secret']) || !isset($_REQUEST['code'])) { ?>

    <div class="wrap">
        <div class="container">
            <form method="POST">
                <label>Домен(полностью): <input type="text" name="referrer"></label> <br>
                <label>Email администратора: <input type="text" name="email"></label> <br>
                <label>ID интеграции: <input type="text" name="client_id"></label> <br>
                <label>Секретный ключ: <input type="text" name="client_secret"></label> <br>
                <label>Код авторизации: <input type="text" name="code"></label> <br>
                <button type="submit">Отправить</button>
            </form>
        </div>
    </div>
    
    <style>
    .wrap {
        padding: 100px 30px;
    }
    .container {
        width: 400px;
        margin: auto
    }
    
    </style>
        
    <?php 
    
    return; 
    
}




$domain = $_REQUEST['referrer'];

$data = [
    'client_id' => $_REQUEST['client_id'],
    'client_secret' => $_REQUEST['client_secret'],
    'grant_type' => 'authorization_code',
    'code' => $_REQUEST['code'],
    'redirect_uri' => 'https://marketplace.market.ru/market-start/',
];

// file_put_contents(__DIR__ . '/log.log', print_r($_REQUEST, true));

$link = 'https://' . $domain . '/oauth2/access_token'; 
$curl = curl_init(); 

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-oAuth-client/1.0');
curl_setopt($curl, CURLOPT_URL, $link);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

$out = curl_exec($curl);

curl_close($curl);

echo '<pre>' . print_r($out, true) . '</pre>';


$response = json_decode($out, true);

$new_token = [
    'access_token' => $response['access_token'],
    'refresh_token' => $response['refresh_token'],
    'expires' => time() + (int)$response['expires_in'],
    'base_domain' => $domain,
    'token_type' => $response['token_type']
];

mkdir(__DIR__ . '/widgets/');
mkdir(__DIR__ . '/widgets/amomarket/');
mkdir(__DIR__ . '/widgets/amomarket/data/');
mkdir(__DIR__ . '/widgets/amomarket/data/' . $domain);
mkdir(__DIR__ . '/widgets/amomarket/data/' . $domain . '/tokens/');

$file = __DIR__ .  '/widgets/amomarket/data/' . $domain . '/tokens/tokens.json';
file_put_contents($file, json_encode($new_token));

$file = __DIR__ . '/widgets/amomarket/data/' . $domain . '/tokens/amo.json';
file_put_contents($file, json_encode([
    'client_id' => $_REQUEST['client_id'],
    'client_secret' => $_REQUEST['client_secret'],
]));

try {
    

$domain_post = get_page_by_title($domain, 'OBJECT', 'domain');

// if ($domain_post) {
//     return;
// }

$post_id = wp_insert_post(wp_slash([
    'post_title' => $domain,
    'post_name' => $domain,
    'post_type' => 'domain',
    'post_status' => 'publish',
]));

update_field('email', $_REQUEST['email'], $post_id);

echo 'OK';

// $link = 'https://'. $domain . '/api/v4/users'; 
// $curl = curl_init(); 

// $auth = 'Authorization: ' . $new_token['token_type'] . ' ' . $new_token['access_token'];
// echo $auth, '<br>';

// curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($curl, CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
// curl_setopt($curl, CURLOPT_URL, $link);
// curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json', $auth]);
// curl_setopt($curl, CURLOPT_HEADER, false);
// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

// $out = curl_exec($curl);

// curl_close($curl);

// echo '<pre>' . print_r($out, true) . '</pre>';


} catch (\Throwable $th) {
    echo '<pre>' . print_r($th, true) . '</pre>';
}