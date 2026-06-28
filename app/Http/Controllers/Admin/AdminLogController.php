<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminLogController extends Controller
{
    /** GET /admin/logs — recent application log entries (read-only). */
    public function index()
    {
        $path = storage_path('logs/laravel.log');
        $entries = [];

        if (is_file($path)) {
            // Read only the tail of the file to stay cheap on large logs.
            $maxBytes = 256 * 1024;
            $size = filesize($path);
            $fh = fopen($path, 'rb');
            if ($size > $maxBytes) {
                fseek($fh, -$maxBytes, SEEK_END);
                fgets($fh); // discard a partial first line
            }
            $content = stream_get_contents($fh);
            fclose($fh);

            // Split on the leading "[YYYY-MM-DD HH:MM:SS]" timestamp of each entry.
            $parts = preg_split('/(?=^\[\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2})/m', $content, -1, PREG_SPLIT_NO_EMPTY);

            foreach (array_reverse($parts) as $part) {
                if (! preg_match('/^\[(?<ts>[^\]]+)\]\s+\S+\.(?<level>[A-Z]+):\s*(?<msg>.*)/s', $part, $m)) {
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

        return view('admin.logs', compact('entries'));
    }
}
