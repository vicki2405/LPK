<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseBackupController extends Controller
{
    /**
     * Display database overview, statistics, and backup/restore control room.
     */
    public function index(): Response
    {
        $databaseName = DB::connection()->getDatabaseName();
        $tablesResult = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
        $totalTables = count($tablesResult);

        // Hitung total ukuran database dari information_schema
        $sizeBytes = 0;
        try {
            $sizeResult = DB::select("
                SELECT SUM(data_length + index_length) AS size_bytes 
                FROM information_schema.TABLES 
                WHERE table_schema = ?
            ", [$databaseName]);
            $sizeBytes = (int) ($sizeResult[0]->size_bytes ?? 0);
        } catch (\Throwable $e) {
            $sizeBytes = 0;
        }

        return Inertia::render('Admin/Database/Index', [
            'databaseStats' => [
                'name' => $databaseName,
                'total_tables' => $totalTables,
                'size_formatted' => $this->formatBytes($sizeBytes),
            ],
        ]);
    }

    /**
     * Stream full database SQL dump (.sql) for download.
     */
    public function download(): StreamedResponse
    {
        $databaseName = DB::connection()->getDatabaseName();
        $filename = 'backup_lms_lpk_' . date('Y-m-d_His') . '.sql';

        return response()->streamDownload(function () use ($databaseName) {
            $pdo = DB::connection()->getPdo();

            // 1. Header Metadata SQL
            echo "-- ==============================================================\n";
            echo "-- SISTEM LMS & CBT TERPADU LPK PENYALURAN JEPANG\n";
            echo "-- DATABASE BACKUP ARCHIVE (FULL DATA & STRUCTURE)\n";
            echo "-- Database: {$databaseName}\n";
            echo "-- Tanggal & Waktu: " . date('Y-m-d H:i:s') . "\n";
            echo "-- ==============================================================\n\n";
            echo "SET FOREIGN_KEY_CHECKS=0;\n";
            echo "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
            echo "SET time_zone = \"+00:00\";\n\n";

            flush();

            // 2. Ambil seluruh tabel basis data
            $tablesResult = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');

            foreach ($tablesResult as $row) {
                $tableName = array_values((array) $row)[0];

                echo "-- --------------------------------------------------------\n";
                echo "-- Struktur Tabel `{$tableName}`\n";
                echo "-- --------------------------------------------------------\n";
                echo "DROP TABLE IF EXISTS `{$tableName}`;\n";

                // Ambil DDL CREATE TABLE
                $createResult = DB::select("SHOW CREATE TABLE `{$tableName}`");
                if (!empty($createResult)) {
                    $createTableSql = ((array) $createResult[0])['Create Table'] ?? null;
                    if ($createTableSql) {
                        echo $createTableSql . ";\n\n";
                    }
                }

                // Dump Data Isi Tabel
                echo "-- Dumping Data untuk Tabel `{$tableName}`\n";
                $totalRows = DB::table($tableName)->count();

                if ($totalRows > 0) {
                    $batchSize = 250;
                    for ($offset = 0; $offset < $totalRows; $offset += $batchSize) {
                        $rows = DB::table($tableName)->offset($offset)->limit($batchSize)->get();
                        if ($rows->isEmpty()) {
                            break;
                        }

                        $insertValues = [];
                        $columns = array_keys((array) $rows->first());
                        $escapedColumns = array_map(fn($col) => "`{$col}`", $columns);
                        $columnList = implode(', ', $escapedColumns);

                        foreach ($rows as $item) {
                            $itemArray = (array) $item;
                            $values = [];
                            foreach ($columns as $col) {
                                $val = $itemArray[$col] ?? null;
                                if (is_null($val)) {
                                    $values[] = 'NULL';
                                } elseif (is_bool($val)) {
                                    $values[] = $val ? '1' : '0';
                                } elseif (is_numeric($val) && !is_string($val)) {
                                    $values[] = $val;
                                } else {
                                    $values[] = $pdo->quote((string) $val);
                                }
                            }
                            $insertValues[] = '(' . implode(', ', $values) . ')';
                        }

                        echo "INSERT INTO `{$tableName}` ({$columnList}) VALUES\n";
                        echo implode(",\n", $insertValues) . ";\n";
                        flush();
                    }
                }

                echo "\n";
                flush();
            }

            echo "-- ==============================================================\n";
            echo "-- AKHIR DATABASE DUMP\n";
            echo "SET FOREIGN_KEY_CHECKS=1;\n";
            echo "-- ==============================================================\n";
            flush();
        }, $filename, [
            'Content-Type' => 'application/sql; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Restore database from uploaded .sql backup file.
     */
    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_file' => 'required|file|max:102400', // Maksimal 100MB
            'confirmation' => 'required|string|in:RESTORE',
        ], [
            'backup_file.required' => 'Silakan pilih berkas file backup (.sql) terlebih dahulu.',
            'backup_file.file' => 'Berkas file backup tidak valid.',
            'backup_file.max' => 'Ukuran berkas file backup maksimal 100MB.',
            'confirmation.in' => 'Anda harus mengetik kata konfirmasi "RESTORE" dengan benar untuk melanjutkan.',
        ]);

        $file = $request->file('backup_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['sql', 'txt'])) {
            return redirect()->back()->with('error', 'Hanya berkas berekstensi .sql yang didukung untuk proses restore.');
        }

        $realPath = $file->getRealPath();
        if (!$realPath || !file_exists($realPath)) {
            return redirect()->back()->with('error', 'Gagal membaca berkas file upload.');
        }

        try {
            // Tingkatkan batas eksekusi waktu & memori sementara untuk database besar
            @set_time_limit(300);
            @ini_set('memory_limit', '512M');

            $sqlContent = file_get_contents($realPath);
            if (empty(trim($sqlContent))) {
                return redirect()->back()->with('error', 'Berkas backup kosong dan tidak berisi instruksi SQL.');
            }

            // Eksekusi SQL dump dengan proteksi foreign key
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::unprepared($sqlContent);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            Log::info('Database restored successfully by Admin: ' . (auth()->user()?->email ?? 'admin'));

            return redirect()->back()->with('success', 'Basis data berhasil dipulihkan (restore) secara sempurna dari berkas backup!');
        } catch (\Throwable $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            Log::error('Database restore error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Gagal memulihkan database: ' . $e->getMessage());
        }
    }

    /**
     * Helper to format bytes to human readable format.
     */
    private function formatBytes(int|float $bytes, int $precision = 2): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
