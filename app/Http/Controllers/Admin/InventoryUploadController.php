<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Http\Requests\Validations\InventoryImportRequest;
use App\Http\Requests\Validations\InventoryUploadRequest;
use App\Jobs\ProcessInventoryCsvBulkUpload as ProcessUpload;

class InventoryUploadController extends Controller
{
    /**
     * Show upload form
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        return view('admin.inventory._upload_form');
    }

    /**
     * Upload the csv file and generate the review table
     *
     * @param  InventoryUploadRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function upload(InventoryUploadRequest $request)
    {
        $path = $request->file('inventories')->getRealPath();
        $records = array_map('str_getcsv', file($path));

        // Validations check for csv_import_limit
        if ((count($records) - 1) > get_csv_import_limit()) {
            $err = (new MessageBag)->add('error', trans('validation.upload_rows', ['rows' => get_csv_import_limit()]));

            return back()->withErrors($err);
        }

        // Get field names from header column
        $fields = array_map('strtolower', $records[0]);

        // Remove the header column
        array_shift($records);

        $rows = [];
        foreach ($records as $record) {
            if (count($fields) != count($record)) {
                $err = (new MessageBag)->add('error', trans('validation.csv_upload_invalid_data'));

                return back()->withErrors($err);
            }

            // Decode unwanted html entities
            $record =  array_map("html_entity_decode", $record);

            // Set the field name as key
            $temp = array_combine($fields, $record);

            // Get the clean data
            $rows[] = clear_encoding_str($temp);
        }

        return view('admin.inventory.upload_review', compact('rows'));
    }

    /**
     * Perform import action
     *
     * @param  InventoryImportRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function import(InventoryImportRequest $request)
    {
        if (config('app.demo') == true) {
            return redirect()->route('admin.stock.inventory.index')
                ->with('warning', trans('messages.demo_restriction'));
        }

        // Set return route
        $return_to = Auth::user()->isFromPlatform() ? 'admin.catalog.product.index' : 'admin.stock.inventory.index';

        if (config('queue.default') == 'sync') {
            $failed_rows = ProcessUpload::dispatchSync(Auth::user(), $request->input('data'));

            if (is_array($failed_rows) && !empty($failed_rows)) {
                return view('admin.inventory.import_failed', compact('failed_rows'));
            }

            return redirect()->route($return_to)
                ->with('success', trans('messages.imported', ['model' => trans('app.model.inventory')]));
        }

        ProcessUpload::dispatch(Auth::user(), $request->input('data'));

        return redirect()->route($return_to)
            ->with('global_notice', trans('messages.csv_import_process_started'));
    }

    /**
     * Download the template file for inventory csv upload.
     *
     * @return \Response response
     */
    public function downloadTemplate()
    {
        // if (is_catalog_enabled()) {
        //     $pathToFile = public_path('csv_templates/catalog_inventories.csv');
        // } else {
        //     $pathToFile = public_path('csv_templates/inventories.csv');
        // }

        $file = is_catalog_enabled() ? 'catalog_inventories.csv' : 'inventories.csv';

        return response()->download(public_path("csv_templates/" . $file));
    }

    /**
     * Download the failed rows as an Excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|\Symfony\Component\HttpFoundation\BinaryFileResponse an Excel file that contains the failed rows
     */
    public function downloadFailedRows(Request $request)
    {
        foreach ($request->input('data') as $row) {
            $data[] = unserialize($row);
        }

        $path = storage_path('failed_rows.xlsx');

        return (new FastExcel(collect($data)))->download($path);
    }
}
