<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminLogController extends Controller
{
    private const ERROR_LEVELS = ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY', 'WARNING'];

    /** GET /admin/logs — recent application log entries (read-only). */
    public function index(Request $request)
    {
        // Default to error-ish levels so a flood of DEBUG/INFO doesn't bury real
        // errors before the 100-entry cap; ?level=all shows everything.
        $showAll  = $request->get('level') === 'all';
        $path     = storage_path('logs/laravel.log');
        $entries  = [];
        $truncated = false;

        if (is_file($path)) {
            $maxBytes = 256 * 1024;
            $size = filesize($path);
            $fh = @fopen($path, 'rb');

            if ($fh !== false) {
                if ($size !== false && $size > $maxBytes) {
                    $truncated = true;
                    fseek($fh, -$maxBytes, SEEK_END);
                    fgets($fh); // discard a partial first line
                }
                $content = stream_get_contents($fh);
                fclose($fh);

                // Split on the leading "[YYYY-MM-DD HH:MM:SS]" timestamp of each entry.
                $parts = preg_split('/(?=^\[\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2})/m', (string) $content, -1, PREG_SPLIT_NO_EMPTY);

                foreach (array_reverse($parts) as $part) {
                    if (! preg_match('/^\[(?<ts>[^\]]+)\]\s+\S+\.(?<level>[A-Z]+):\s*(?<msg>.*)/s', $part, $m)) {
                        continue;
                    }
                    if (! $showAll && ! in_array($m['level'], self::ERROR_LEVELS, true)) {
                        continue;
                    }
                    $message = strtok(trim($m['msg']), "\n");
                    $entries[] = [
                        'ts'      => trim($m['ts']),
                        'level'   => $m['level'],
                        'message' => mb_strimwidth($message, 0, 300, '…'),
                    ];
                    if (count($entries) >= 100) {
                        break;
                    }
                }
            }
        }

        return view('admin.logs', compact('entries', 'showAll', 'truncated'));
    }
}
