<?php
if (!defined('ABSPATH')) exit;

class IntegriCMS_Scanner {

    // Recursively scan directories and generate file hashes
    public static function scan_directory($directory) {
        $results = [];
        if (!is_dir($directory)) return $results;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                // Skip cache, logs, or backup files
                if (preg_match('/(cache|backup|error_log|\.log|\.zip|\.tar)$/i', $file->getFilename())) {
                    continue;
                }

                $path = str_replace(ABSPATH, '', $file->getPathname());
                $hash = hash_file('sha256', $file->getPathname());
                $results[$path] = $hash;
            }
        }

        return $results;
    }

    // Perform the first scan after installation
    public static function initial_scan() {
        $dirs = [ABSPATH]; // scan entire WordPress root
        $all_hashes = [];

        foreach ($dirs as $dir) {
            $all_hashes += self::scan_directory($dir);
        }

        IntegriCMS_DB::save_initial_hashes($all_hashes);
    }

    // Compare hashes with the base reference
    public static function compare_to_base() {
        $dirs = [ABSPATH];
        $base_hashes = IntegriCMS_DB::get_base_hashes();
        $changes = [];

        foreach ($dirs as $dir) {
            $new_hashes = self::scan_directory($dir);
            foreach ($new_hashes as $path => $hash) {
                if (isset($base_hashes[$path])) {
                    if ($base_hashes[$path] !== $hash) {
                        $changes[] = $path;
                        IntegriCMS_DB::update_current_hash($path, $hash, 'MODIFIED');
                    } else {
                        IntegriCMS_DB::update_current_hash($path, $hash, 'OK');
                    }
                }
            }
        }

        return $changes;
    }
}
