<?php

namespace App\Http\Controllers\Admin;

class PaymentImportController extends Controller
{
    public function __construct()
    {
        $this->setTitle(__('Import Payment Data'));
    }

    public function index()
    {
        return view('admin.import_payments.index', compact('import_payments'));
    }

    private function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $header = null;
        $data = [];
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

    public function importCsv()
    {
        $file = public_path('file/test.csv');

        $customerArr = $this->csvToArray($file);

        for ($i = 0; $i < count($customerArr); $i++) {
            User::firstOrCreate($customerArr[$i]);
        }

        return 'Done';
    }
}
