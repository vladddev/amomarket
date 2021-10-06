<?php

class PermissionsWorker {
    private $widget_id;
    private $domain_id;
    private $order_id = 0;
    private $enabled;
    private $error = [];


    public function __construct($widget_id, $domain_id) {
        $this->widget_id = $widget_id;
        $this->domain_id = $domain_id;
        $this->$order_id = $this->get_order_id();

        $this->check_permissions();
    }

    private function check_permissions()
    {

        if ($this->$order_id == 0) {
            $this->error = [
                'success' => false,
                'code' => 10004,
                'result' => 'No order'
            ];

            return;
        }

        $price_type = get_field('price_type', $this->$order_id);

        if ($price_type != 'free') {
            $balance = $this->check_balance();
            if ($balance <= 0) {
                $this->error = [
                    'success' => false,
                    'code' => 10005,
                    'result' => 'Your balance is empty'
                ];

                return;
            }

            $status = get_field('status', $this->$order_id);
            if ($status == 'off') {
                $this->error = [
                    'success' => false,
                    'code' => 10001,
                    'result' => 'Your order is disabled'
                ];

                return;
            }

            if ($price_type != 'once') {
                $is_expired = $this->check_expiry_date();

                if ($is_expired) {
                    $this->error = [
                        'success' => false,
                        'code' => 10002,
                        'result' => 'Your order is expired'
                    ];

                    return;
                }
            }
        }

        

        $this->enabled = true;
    }

    private function check_expiry_date()
    {
        $expiry = get_field('expired', $this->$order_id);
        if ($expiry) {
            return $expiry < time();
        } else {
            return false;
        }
        
    }

    private function get_order_id()
    {
        $order = 0;

        $query = new WP_Query([
            'post_type' => 'widget-order',
            'posts_per_page' => 1,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'widget',
                    'value' => $this->widget_id
                ],
                [
                    'key' => 'domain',
                    'value' => $this->domain_id
                ]
            ]
        ]);
                    
        if ($query->have_posts()) {
            while ($query->have_posts()) { $query->the_post();
                $order = get_the_ID();
            }
            wp_reset_postdata();
        } 

        return $order;
    }

    private function check_balance()
    {
        return floatval(get_field('balance', $this->domain_id));
    }

    public function is_widget_enable()
    {
        return $this->enabled;
    }

    public function get_error()
    {
        return $this->error;
    }
}


























