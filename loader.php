<?php

include_once '../../../wp-load.php';
include 'functions.php';

function set_widget($data) {
    $tokens_dir = __DIR__ . '/widgets/' . $data['slug'] . '/data/' . $data['domain'] . '/tokens/';
    $settings_dir = __DIR__ . '/widgets/' . $data['slug'] . '/data/' . $data['domain'] . '/settings/';

    mkdir($tokens_dir);
	mkdir($settings_dir);

    if (isset($data['amo'])) {
        file_put_contents($tokens_dir . 'amo.json', $data['amo']);
    }
    if (isset($data['tokens'])) {
        file_put_contents($tokens_dir . 'tokens.json', $data['tokens']);
    }
    
    if (isset($data['title'])) {
        $widget_post = get_page_by_title($data['title'], 'OBJECT', 'widget');
        if (!$widget_post) {
            $post_id = wp_insert_post(wp_slash([
                'post_title' => $data['title'],
                'post_name' => $data['slug'],
                'post_type' => 'widget',
                'post_status' => 'publish',
            ]));
        
            update_field('slug', $data['slug'], $post_id);

            if (file_exists(__DIR__ . '/widgets/' . $data['slug'])) return;
		
            mkdir(__DIR__ . '/widgets/' . $data['slug']);
            $files_dir = __DIR__ . '/widgets/' . $data['slug'] . '/files';
            mkdir($files_dir);
            file_put_contents($files_dir . '/index.php', '');
            file_put_contents($files_dir . '/view.php', '');
            file_put_contents($files_dir . '/hook_name.php', '');
            mkdir($files_dir . '/assets/');
            mkdir($files_dir . '/assets/css/');
            mkdir($files_dir . '/assets/js/');
            file_put_contents($files_dir . '/assets/js/once.js', '');
            file_put_contents($files_dir . '/assets/js/always.js', '');
            mkdir(__DIR__ . '/widgets/' . $data['slug'] . '/data/');
        }
    }
}



