<body>
<div class="header">
        <div class="team-name">Codesisters</div>
        <h1 class="title">🛡️ IntegriCMS</h1>
        <p class="subtitle">File Integrity Monitor for Content Management Systems</p>
    </div>

    <div class="content">
        <div class="section">
            <h2 class="section-title">About IntegriCMS</h2>
            <p class="description">
                IntegriCMS is an open-source tool designed to protect file integrity in Content Management Systems such as WordPress. Its main purpose is to detect unauthorized modifications in system files and templates that could compromise website security.
            </p>
        </div>

        <div class="section">
            <h2 class="section-title">Integrity Scan</h2>
            <div class="scan-section">
                <p class="scan-description">
                    This feature checks if your WordPress files have been modified, replaced, or corrupted. Click the button below to run a complete integrity scan.
                </p>

                <form method="post">
                    <?php wp_nonce_field('integricms_scan_action', 'integricms_scan_nonce'); ?>
                    <?php submit_button('🔍 Run Integrity Scan Now', 'primary', 'submit', false, [
                        'class' => 'scan-btn',
                        'style' => 'margin-bottom:20px;'
                    ]); ?>
                </form>

                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST'
                    && isset($_POST['integricms_scan_nonce'])
                    && wp_verify_nonce($_POST['integricms_scan_nonce'], 'integricms_scan_action')) {

                    $changes = IntegriCMS_Scanner::compare_to_base();

                    if (empty($changes)) {
                        echo '<div class="results success">';
                        echo '<div class="results-title">✅ All files are safe</div>';
                        echo '<p>No unauthorized modifications detected. Your WordPress installation is secure.</p>';
                        echo '</div>';
                    } else {
                        echo '<div class="results warning">';
                        echo '<div class="results-title">⚠️ Warning: Modified files detected</div>';
                        echo '<p>The following files have been modified since the last scan:</p>';
                        echo '<table class="results-table">';
                        echo '<thead><tr><th>File Path</th></tr></thead><tbody>';
                        foreach ($changes as $file) {
                            echo '<tr><td>' . esc_html($file) . '</td></tr>';
                        }
                        echo '</tbody></table>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Key Features</h2>
            <div class="features-grid">
                <div class="feature-card"><div class="feature-number">1</div><div class="feature-title">File Scanning</div><p class="feature-desc">Periodically analyzes CMS files and templates to ensure system integrity.</p></div>
                <div class="feature-card"><div class="feature-number">2</div><div class="feature-title">Cryptographic Hash Generation</div><p class="feature-desc">Generates a cryptographic hash for each file and stores it in the database.</p></div>
                <div class="feature-card"><div class="feature-number">3</div><div class="feature-title">Hash Comparison</div><p class="feature-desc">Compares current hashes with previously stored ones to detect modifications.</p></div>
                <div class="feature-card"><div class="feature-number">4</div><div class="feature-title">Modification Detection</div><p class="feature-desc">Identifies files that have been altered or manipulated after the initial scan.</p></div>
                <div class="feature-card"><div class="feature-number">5</div><div class="feature-title">Automatic Alerts</div><p class="feature-desc">Notifies the site administrator about any suspicious changes detected.</p></div>
                <div class="feature-card"><div class="feature-number">6</div><div class="feature-title">File Restoration</div><p class="feature-desc">Allows restoration of modified files to their original state.</p></div>
                <div class="feature-card"><div class="feature-number">7</div><div class="feature-title">Comprehensive Logging</div><p class="feature-desc">Records analysis results and actions taken via email or system alerts.</p></div>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Downloads</h2>
            <div class="download-section">
                <p style="color: #4a5568; margin-bottom: 20px;">Access our repositories and documentation</p>
                <a href="#" class="download-btn">GitHub Repository</a>
                <a href="#" class="download-btn">Documentation</a>
            </div>
        </div>
    </div>

    <div class="credits">
        <h3 class="credits-title">Project Credits</h3>
        <div class="credits-content">
            <div class="credits-left">
                <div class="credits-label">Development Team</div>
                <div class="credits-value">
                    Eylin Cabrera Lukes<br>
                    Rosicela Cubero Flores<br>
                    Glenda Moraga Gutiérrez
                </div>
            </div>
            <div class="credits-right">
                <div class="credits-label">Academic Information</div>
                <div class="credits-value">
                    Advanced Topics in Databases<br>
                    Manuel Espinoza Guerrero<br>
                    Universidad Nacional de Costa Rica
                </div>
            </div>
            <div class="credits-bottom">
                <div class="credits-label">Version & Date</div>
                <div class="credits-value">Version 2.0 | November 7, 2025</div>
            </div>
        </div>
    </div>
</body>
