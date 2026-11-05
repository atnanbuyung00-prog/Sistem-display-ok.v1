<?php

namespace App\Http\Controllers;

use App\Services\DatabaseBackupService;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Exception;

class BackupController extends Controller
{
    protected DatabaseBackupService $backupService;

    public function __construct(DatabaseBackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    public function index()
    {
        $backups = $this->backupService->getBackups();
        $stats = $this->backupService->getDatabaseStats();

        return view('admin.backups.index', compact('backups', 'stats'));
    }

    public function store(Request $request)
    {
        try {
            $customName = $request->input('custom_name');
            $result = $this->backupService->createBackup($customName);

            ActivityLog::record('BACKUP', 'Database', "Membuat cadangan database: {$result['filename']} ({$result['formatted_size']})", [
                'new_values' => $result,
            ]);

            return back()->with('success', "Cadangan database berhasil dibuat: {$result['filename']} ({$result['formatted_size']})");
        } catch (Exception $e) {
            return back()->with('error', "Gagal membuat cadangan database: " . $e->getMessage());
        }
    }

    public function download(string $filename)
    {
        $safeName = basename($filename);
        $path = DatabaseBackupService::getBackupDirectory() . DIRECTORY_SEPARATOR . $safeName;

        if (!File::exists($path)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        ActivityLog::record('BACKUP', 'Database', "Mengunduh file backup: {$safeName}");

        return response()->download($path, $safeName, [
            'Content-Type' => 'application/sql',
        ]);
    }

    public function restore(Request $request)
    {
        try {
            $filename = null;
            $path = null;

            if ($request->hasFile('backup_file')) {
                $file = $request->file('backup_file');
                $ext = strtolower($file->getClientOriginalExtension());
                if ($ext !== 'sql') {
                    return back()->with('error', 'Format file tidak didukung. Harap upload file berformat .sql');
                }

                $filename = 'uploaded_' . date('Ymd_His') . '_' . $file->getClientOriginalName();
                $destDir = DatabaseBackupService::getBackupDirectory();
                $file->move($destDir, $filename);
                $path = $destDir . DIRECTORY_SEPARATOR . $filename;
            } elseif ($request->filled('filename')) {
                $filename = basename($request->input('filename'));
                $path = DatabaseBackupService::getBackupDirectory() . DIRECTORY_SEPARATOR . $filename;
            } else {
                return back()->with('error', 'Silakan pilih file backup yang ingin dipulihkan.');
            }

            if (!$path || !File::exists($path)) {
                return back()->with('error', 'File backup tidak ditemukan pada server.');
            }

            // Perform restoration
            $this->backupService->restoreFromFile($path);

            ActivityLog::record('RESTORE', 'Database', "Memulihkan (Restore) database dari file: {$filename}", [
                'new_values' => ['filename' => $filename, 'restored_at' => now()->toDateTimeString()],
            ]);

            return back()->with('success', "Database berhasil dipulihkan (Restore) dari file {$filename}. Seluruh data telah kembali ke titik cadangan.");
        } catch (Exception $e) {
            return back()->with('error', "Gagal memulihkan database: " . $e->getMessage());
        }
    }

    public function destroy(string $filename)
    {
        $safeName = basename($filename);
        $deleted = $this->backupService->deleteBackup($safeName);

        if ($deleted) {
            ActivityLog::record('HAPUS', 'Database', "Menghapus file backup cadangan: {$safeName}");
            return back()->with('success', "File backup {$safeName} berhasil dihapus.");
        }

        return back()->with('error', "Gagal menghapus file backup {$safeName}.");
    }
}
