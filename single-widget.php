<?php
// Работа с виджетом (из виджета) - сохранение и получение настроек, обработка хуков

$widget_id = get_queried_object_id();


if (isset($_GET['name']) && isset($_POST['account'])) {
    $widget_name = get_field('slug', $widget_id);

    $url = get_template_directory_uri() . '/' . $widget_name . '/files/hook_' . $_GET['name'] . '.php';
    $ch = curl_init();  

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);

    $content = curl_exec($ch); 

    curl_close($ch);

    exit(200);

}


include __DIR__ . '/classes/PermissionsWorker.php';
include __DIR__ . '/classes/SettingsWorker.php';

$domain = $_REQUEST['amodomain']; // domain
$widget_id = get_queried_object_id();


$domain_id = get_page_by_title($domain, 'OBJECT', 'domain')->ID;


if ($domain_id == 0) {
    echo json_decode([
        'success' => false,
        'code' => 10003,
        'result' => 'Incorrect domain or email'
    ]);

    return;
}

$permissions_worker = new PermissionsWorker($widget_id, $domain_id);
if ($permissions_worker->is_widget_enable()) {
    $settings_worker = new SettingsWorker($widget_id, $domain_id);

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {    
        echo json_encode([
            'success' => true,
            'code' => 10000,
            'result' => [
                'settings' => $settings_worker->get_settings(),
                'auth_data' => $settings_worker->get_auth_data()
            ]
        ]);
    } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (isset($_POST['settings'])) {
            $settings_worker->set_settings($_POST['settings']);
        }
        if (isset($_POST['auth_data'])) {
            $settings_worker->set_auth_data($_POST['auth_data']);
        }

        echo json_encode([
            'success' => true,
            'code' => 10000,
            'result' => true
        ]);
    };

} else {
    echo json_encode($permissions_worker->get_error());
}