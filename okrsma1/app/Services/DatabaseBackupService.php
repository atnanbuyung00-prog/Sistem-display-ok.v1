<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Exception;

class DatabaseBackupService
{
    /**
     * Get the directory where backups are stored.
     */
    public static function getBackupDirectory(): string
    {
        $dir = storage_path('app/backups');
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }
        return $dir;
    }

    /**
     * Create a complete SQL database dump.
     */
    public function createBackup(?string $customName = null): array
    {
        $dir = self::getBackupDirectory();
        $dateStr = now('Asia/Jakarta')->format('Y-m-d_His');
        $dbName = config('database.connections.mysql.database', 'operasi');

        $filename = $customName
            ? preg_replace('/[^a-zA-Z0-9_\-]/', '_', $customName) . '.sql'
            : "backup_{$dbName}_{$dateStr}.sql";

        $filePath = $dir . DIRECTORY_SEPARATOR . $filename;

        // Fetch all tables
        $tables = DB::select('SHOW TABLES');
        $tableNames = [];
        foreach ($tables as $t) {
            foreach ((array)$t as $name) {
                $tableNames[] = $name;
            }
        }

        $pdo = DB::getPdo();
        $sql = "-- ========================================================\n";
        $sql .= "-- OK RS Mutiara Aini - Database Backup\n";
        $sql .= "-- Database: {$dbName}\n";
        $sql .= "-- Date: " . now('Asia/Jakarta')->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- Server: " . php_uname('n') . "\n";
        $sql .= "-- ========================================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
        $sql .= "SET time_zone = '+07:00';\n\n";

        foreach ($tableNames as $table) {
            // Table structure
            $create = DB::select("SHOW CREATE TABLE `{$table}`");
            $createSql = ((array)$create[0])['Create Table'];
            $sql .= "-- --------------------------------------------------------\n";
            $sql .= "-- Table structure for `{$table}`\n";
            $sql .= "-- --------------------------------------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $createSql . ";\n\n";

            // Table data
            $rows = DB::table($table)->get();
            if ($rows->count() > 0) {
                $sql .= "-- Dumping data for `{$table}` (" . $rows->count() . " rows)\n";
                $cols = array_keys((array)$rows[0]);
                $escapedCols = array_map(fn($c) => "`{$c}`", $cols);
                $colStr = implode(', ', $escapedCols);

                // Insert in batches of 100 for safety and readability
                $chunks = $rows->chunk(100);
                foreach ($chunks as $chunk) {
                    $sql .= "INSERT INTO `{$table}` ({$colStr}) VALUES\n";
                    $rowValues = [];
                    foreach ($chunk as $row) {
                        $vals = [];
                        foreach ((array)$row as $val) {
                            if (is_null($val)) {
                                $vals[] = 'NULL';
                            } else {
                                $vals[] = $pdo->quote((string)$val);
                            }
                        }
                        $rowValues[] = "  (" . implode(', ', $vals) . ")";
                    }
                    $sql .= implode(",\n", $rowValues) . ";\n\n";
                }
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        $sql .= "-- End of backup\n";

        File::put($filePath, $sql);

        $size = File::size($filePath);
        return [
            'filename'       => $filename,
            'path'           => $filePath,
            'size'           => $size,
            'formatted_size' => self::formatBytes($size),
            'created_at'     => now('Asia/Jakarta'),
            'table_count'    => count($tableNames),
        ];
    }

    /**
     * Get list of all backup files ordered by newest.
     */
    public function getBackups(): array
    {
        $dir = self::getBackupDirectory();
        $files = File::glob($dir . '/*.sql');

        $list = [];
        foreach ($files as $file) {
            $filename = basename($file);
            $size = File::size($file);
            $modified = File::lastModified($file);

            $list[] = [
                'filename'       => $filename,
                'path'           => $file,
                'size'           => $size,
                'formatted_size' => self::formatBytes($size),
                'created_at'     => \Carbon\Carbon::createFromTimestamp($modified, 'Asia/Jakarta'),
                'timestamp'      => $modified,
            ];
        }

        // Sort descending by timestamp
        usort($list, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $list;
    }

    /**
     * Restore database from a given SQL file path.
     */
    public function restoreFromFile(string $filePath): bool
    {
        if (!File::exists($filePath)) {
            throw new Exception("File backup tidak ditemukan: {$filePath}");
        }

        $sql = File::get($filePath);
        if (empty(trim($sql))) {
            throw new Exception("File backup kosong atau tidak valid.");
        }

        // Execute queries
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::unprepared($sql);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Clear compiled views & cache
        try {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
        } catch (\Throwable $th) {
            // ignore cache clear errors
        }

        return true;
    }

    /**
     * Delete a backup file.
     */
    public function deleteBackup(string $filename): bool
    {
        $safeName = basename($filename);
        $filePath = self::getBackupDirectory() . DIRECTORY_SEPARATOR . $safeName;
        if (File::exists($filePath)) {
            return File::delete($filePath);
        }
        return false;
    }

    /**
     * Get health & statistics of the current database.
     */
    public function getDatabaseStats(): array
    {
        $dbName = config('database.connections.mysql.database', 'operasi');

        $sizeResult = DB::select("
            SELECT SUM(data_length + index_length) AS db_size
            FROM information_schema.TABLES
            WHERE table_schema = ?
        ", [$dbName]);

        $dbSizeBytes = (int) ($sizeResult[0]->db_size ?? 0);

        return [
            'database'        => $dbName,
            'size_bytes'      => $dbSizeBytes,
            'formatted_size'  => self::formatBytes($dbSizeBytes),
            'tables_count'    => count(DB::select('SHOW TABLES')),
            'schedules_count' => DB::table('surgery_schedules')->count(),
            'patients_count'  => DB::table('patients')->count(),
            'users_count'     => DB::table('users')->count(),
            'logs_count'      => DB::table('activity_logs')->count(),
        ];
    }

    /**
     * Helper to format bytes into readable units.
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
