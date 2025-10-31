<?php
if (!defined('ABSPATH')) exit;

class IntegriCMS_DB {

    //Create main table for file hashes
    public static function create_table() {
        global $wpdb;
        $table = $wpdb->prefix . 'integricms_hashes';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table (
            id INT(11) NOT NULL AUTO_INCREMENT,
            file_path TEXT NOT NULL,
            hash_base VARCHAR(255) NOT NULL,
            hash_current VARCHAR(255) DEFAULT NULL,
            status VARCHAR(20) DEFAULT 'OK',
            scan_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    //Create logs table for automatic jobs
    public static function create_logs_table() {
        global $wpdb;
        $table = $wpdb->prefix . 'integricms_logs';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table (
            id INT(11) NOT NULL AUTO_INCREMENT,
            date DATETIME DEFAULT CURRENT_TIMESTAMP,
            status VARCHAR(20) DEFAULT 'OK',
            details TEXT,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    //Save initial hashes (first scan)
    public static function save_initial_hashes($hashes) {
        global $wpdb;
        $table = $wpdb->prefix . 'integricms_hashes';

        foreach ($hashes as $path => $hash) {
            $wpdb->insert($table, [
                'file_path' => $path,
                'hash_base' => $hash
            ]);
        }
    }

    //Retrieve base hashes
    public static function get_base_hashes() {
        global $wpdb;
        $table = $wpdb->prefix . 'integricms_hashes';
        $results = $wpdb->get_results("SELECT file_path, hash_base FROM $table", OBJECT_K);
        return wp_list_pluck($results, 'hash_base');
    }

    //Update current hash and status
    public static function update_current_hash($path, $hash, $status = 'OK') {
        global $wpdb;
        $table = $wpdb->prefix . 'integricms_hashes';
        $wpdb->update(
            $table,
            [
                'hash_current' => $hash,
                'status' => $status,
                'scan_date' => current_time('mysql')
            ],
            ['file_path' => $path]
        );
    }

    //Log job event
    public static function log_event($status, $details) {
        global $wpdb;
        $table = $wpdb->prefix . 'integricms_logs';
        $wpdb->insert($table, [
            'status' => $status,
            'details' => $details
        ]);
    }

    // Send email alert if files are modified
    public static function send_email_notification($changes) {
        $to = get_option('admin_email');
        $subject = '🚨 IntegriCMS Alert: File Integrity Compromised';

        $body = '<h2 style="color:#2c5282;">IntegriCMS - Integrity Report</h2>';
        $body .= '<p>The following files were modified:</p>';
        $body .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">';
        $body .= '<tr style="background:#edf2f7;"><th>File Path</th></tr>';
        foreach ($changes as $file) {
            $body .= '<tr><td>' . esc_html($file) . '</td></tr>';
        }
        $body .= '</table>';
        $body .= '<p>Report generated automatically by <strong>IntegriCMS</strong> on ' . current_time('mysql') . '</p>';
        $body .= '<p><em>Developed by CodeSisters - UNA</em></p>';

        $headers = ['Content-Type: text/html; charset=UTF-8'];
        wp_mail($to, $subject, $body, $headers);
    }
}
