<?php

namespace Newnet\Setting\Http\Controllers\Admin;

use Artisan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;

class UpgradeController extends Controller
{
    public function index()
    {
        admin_can('upgrade');

        $statusOutput = '';
        $hasPending = false;
        $result = null;
        $isRunning = false;
        $webUpgradeDisabled = false;
        $environment = app()->environment();

        if (app()->environment('production') && !config('cms.setting.admin_web_upgrade', false)) {
            $webUpgradeDisabled = true;
        }

        try {
            Artisan::call('migrate:status');
            $statusOutput = Artisan::output();
            $hasPending = str_contains($statusOutput, 'Pending');
            $isRunning = File::exists(storage_path('framework/admin-migrate.lock'));
        } catch (\Throwable $e) {
            $statusOutput = app()->environment('production')
                ? 'Unable to check migration status.'
                : 'Error checking migration status: ' . $e->getMessage();
        }

        return view('setting::admin.upgrade.index', compact('statusOutput', 'hasPending', 'result', 'isRunning', 'webUpgradeDisabled', 'environment'));
    }

    public function run(Request $request)
    {
        admin_can('upgrade');

        if (app()->environment('production') && !config('cms.setting.admin_web_upgrade', false)) {
            $result = [
                'success' => false,
                'output' => '',
                'error' => 'Web‑triggered migration is disabled in production. Use CLI.',
                'exitCode' => null,
            ];
            return $this->getMigrationStatus($result);
        }

        $request->validate([
            'confirm_upgrade' => 'required|accepted',
        ]);

        $result = [
            'success' => false,
            'output' => '',
            'error' => null,
            'exitCode' => null,
        ];
        $lockFile = storage_path('framework/admin-migrate.lock');
        $lockHandle = null;
        $lockAcquired = false;

        try {
            $lockHandle = fopen($lockFile, 'c+');
            if (!$lockHandle || !flock($lockHandle, LOCK_EX | LOCK_NB)) {
                $result['error'] = 'Could not acquire migration lock. Another migration may be in progress.';
                return $this->getMigrationStatus($result);
            }
            $lockAcquired = true;
            ftruncate($lockHandle, 0);
            rewind($lockHandle);
            fwrite($lockHandle, time() + 600);
            fflush($lockHandle);

            if (function_exists('set_time_limit')) {
                @set_time_limit(0);
            }

            $exitCode = Artisan::call('migrate', ['--force' => true]);
            $result['output'] = Artisan::output();
            $result['exitCode'] = $exitCode;
            $result['success'] = $exitCode === 0;

        } catch (\Throwable $e) {
            $result['error'] = $this->sanitizeErrorMessage($e);
            $result['output'] = app()->environment('production') ? '' : Artisan::output();
        } finally {
            if ($lockAcquired && $lockHandle) {
                flock($lockHandle, LOCK_UN);
            }
            if ($lockHandle) {
                fclose($lockHandle);
            }
            if ($lockAcquired && File::exists($lockFile)) {
                File::delete($lockFile);
            }
        }

        return $this->getMigrationStatus($result);
    }

    protected function getMigrationStatus($result = null)
    {
        $statusOutput = '';
        $hasPending = false;
        $isRunning = false;
        $webUpgradeDisabled = false;
        $environment = app()->environment();

        if (app()->environment('production') && !config('cms.setting.admin_web_upgrade', false)) {
            $webUpgradeDisabled = true;
        }

        try {
            Artisan::call('migrate:status');
            $statusOutput = Artisan::output();
            $hasPending = str_contains($statusOutput, 'Pending');
            $isRunning = File::exists(storage_path('framework/admin-migrate.lock'));
        } catch (\Throwable $e) {
            $statusOutput = app()->environment('production')
                ? 'Unable to check migration status.'
                : 'Error checking migration status: ' . $e->getMessage();
        }

        return view('setting::admin.upgrade.index', compact('statusOutput', 'hasPending', 'result', 'isRunning', 'webUpgradeDisabled', 'environment'));
    }

    protected function sanitizeErrorMessage(\Throwable $e)
    {
        return app()->environment('production')
            ? 'A migration error occurred. Check logs for details.'
            : $e->getMessage();
    }
}
