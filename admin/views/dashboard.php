<div class="wrap integricms-wrap">
    <h1 class="integricms-header">🛡️ IntegriCMS - File Integrity Monitor</h1>
    <p class="integricms-info">
        Developed by <strong>CodeSisters</strong><br>
        <em>Universidad Nacional de Costa Rica</em>
    </p>

    <hr>

    <h2 class="integricms-subheader">Integrity Scan</h2>
    <p>This feature checks if your WordPress files have been modified, replaced, or corrupted.</p>

    <form method="post">
        <?php wp_nonce_field('integricms_scan_action', 'integricms_scan_nonce'); ?>
        <?php submit_button('🔍 Run Integrity Scan Now', 'primary', 'submit', false); ?>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['integricms_scan_nonce']) &&
        wp_verify_nonce($_POST['integricms_scan_nonce'], 'integricms_scan_action')) {

        $changes = IntegriCMS_Scanner::compare_to_base();

        if (empty($changes)) {
            echo '<div class="notice notice-success"><p>✅ All files are safe. No changes detected.</p></div>';
        } else {
            echo '<div class="notice notice-error">';
            echo '<p>⚠️ The following files have been modified:</p>';
            echo '<table class="integricms-table">';
            echo '<tr><th>File Path</th></tr>';
            foreach ($changes as $file) {
                echo '<tr><td>' . esc_html($file) . '</td></tr>';
            }
            echo '</table>';
            echo '</div>';
        }
    }
    ?>

    <div class="integricms-footer">
        <hr>
        <h3>Plugin Information</h3>
        <ul>
            <li><strong>Version:</strong> 2.0</li>
            <li><strong>Developer:</strong> CodeSisters</li>
            <li><strong>University:</strong> Universidad Nacional de Costa Rica</li>
            <li><strong>Course:</strong> Advanced Topics in Databases</li>
        </ul>
    </div>
</div>
