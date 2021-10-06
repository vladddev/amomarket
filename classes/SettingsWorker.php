<?php


class SettingsWorker {
    private $widget_name;
    private $domain_name;


    public function __construct($widget_name, $domain_name) {
        $this->widget_name = $widget_name;
        $this->domain_name = $domain_name;
    }

    public function save_settings($data)
    {
        $file = __DIR__ . '/widgets/' . $this->widget_name . '/data/' . $this->domain_name . '/settings/settings.json';
        return file_put_contents($file, $data);
    }

    public function get_settings()
    {
        $file = __DIR__ . '/widgets/' . $this->widget_name . '/data/' . $this->domain_name . '/settings/settings.json';
        return file_get_contents($file);
    }

    public function save_auth_data($data)
    {
        $file = __DIR__ . '/widgets/' . $this->widget_name . '/data/' . $this->domain_name . '/settings/auth_data.json';
        return file_put_contents($file, $data);
    }

    public function get_auth_data()
    {
        $file = __DIR__ . '/widgets/' . $this->widget_name . '/data/' . $this->domain_name . '/settings/auth_data.json';
        return file_get_contents($file);
    }
}



























