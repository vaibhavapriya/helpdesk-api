<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class QueueController extends Controller
{
    public function getDriver()
    {
        return response()->json([
            'driver' => config('queue.default'),
        ]);
    }

    public function setDriver(Request $request)
    {
        $driver = $request->input('driver');
        $allowed = array_keys(config('queue.connections'));

        if (!in_array($driver, $allowed)) {
            return response()->json(['error' => 'Invalid driver'], 422);
        }

        $this->updateEnv('QUEUE_CONNECTION', $driver);
        File::delete(base_path('bootstrap/cache/config.php'));
        return response()->json(['driver' => $driver]);
    }

    protected function updateEnv($key, $value)
    {
        $path = base_path('.env');
        $content = preg_replace(
            "/^{$key}=.*/m",
            "{$key}={$value}",
            File::get($path)
        );
        File::put($path, $content);
    }
}
