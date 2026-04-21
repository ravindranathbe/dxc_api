<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class File
{
    /**
     * Process CSV file
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    public static function processCsv($file)
    {
        $handle = fopen($file, 'r');
        $header = fgetcsv($handle);
        $data = [];
        while ($row = fgetcsv($handle)) {
            $data[] = array_combine($header, $row);
        }
        fclose($handle);
        return $data;
    }

    /**
     * Export array to CSV
     * 
     * @param array $data
     * @return string
     */
    public static function exportCsv($data)
    {
        $handle = fopen('php://temp', 'r+');
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        return stream_get_contents($handle);
    }
}
