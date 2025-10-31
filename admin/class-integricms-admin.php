<?php
if (!defined('ABSPATH')) exit;

class IntegriCMS_Admin {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
    }

    public function add_menu() {
        add_menu_page(
            'IntegriCMS',
            'IntegriCMS',
            'manage_options',
            'integricms',
            [$this, 'render_dashboard'],
            'dashicons-shield-alt'
        );
    }

    public function enqueue_styles($hook) {
        if ($hook !== 'toplevel_page_integricms') return;
        wp_enqueue_style(
            'integricms-admin-style',
            INTEGRICMS_URL . 'admin/css/admin-style.css',
            [],
            '1.0.0'
        );
    }


    public function render_dashboard() {
        include INTEGRICMS_PATH . 'admin/views/dashboard.php';
    }
}
