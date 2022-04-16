<?php

namespace App\Services;

use Response;
use App\CustomField;

class ExportCSV {

	protected $columns = [];
	protected $data = [];
	protected $filename;

	public function __construct($model, $params, $filename = null)
	{
		$exportable = collect($model->exportable())->mapWithKeys(function($item) {
			return [$item => __('lang.'.$item)];
		})->toArray();

		$collection = $model->export(json_decode($params, true));
		$cfCols = [];
		$cf = null;
		if($filename) {
			$cf = CustomField::where('name', $filename)
	        ->first();


	        if($cf) {
	        	$cfCols = collect($cf->fields)->map(function($item) {
	        		return $item['label'];
	        	})->toArray();
	        }
		}

		$exports = $collection->map(function($item) use ($exportable, $cf) {

		    $line = $item->toArray();

		    $row = [];
		    foreach($exportable as $key => $name) {
		        $row[$key] = data_get($line, $key);
		    }

		    $values = json_decode($item->custom_values, true);

		    if($cf) {
		    	foreach($cf->fields as $field) {

		    	    if(isset($values[$field['name']])) {
		    	        $field['model'] = $values[$field['name']];
		    	        $row[$field['name']] = $values[$field['name']];
		    	    } else {
		    	        $row[$field['name']] = null;
		    	    }

		    	}
		    }

		    return $row;
		});

		$this->filename = __('lang.'.$filename);
		$this->columns = array_merge($exportable, $cfCols);
		$this->data = $exports;
	}

	public function toString()
	{
		$name = $this->filename . '-' . now()->format(config('flow.application.date_format'));

		$data = $this->data;
		$columns = $this->columns;

		$fh = fopen('php://temp', 'rw'); # don't create a file, attempt
		# to use memory instead

		# write out the headers
		fputcsv($fh, $columns);

		# write out the data
		foreach ( $data as $row ) {
		        fputcsv($fh, array_values($row));
		}
		rewind($fh);
		$csv = stream_get_contents($fh);
		fclose($fh);

		return $csv;
	}

	public function download()
	{
		// todo settings
		$name = $this->filename . '-' . now()->format(config('flow.application.date_format'));
		$headers = [
		    "Content-type" => "text/csv",
		    "Content-Disposition" => "attachment; filename={$name}.csv",
		    "Pragma" => "no-cache",
		    "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
		    "Expires" => "0"
		];

		$data = $this->data;
		$columns = $this->columns;

		$callback = function() use ($data, $columns)
		{
		    $fh = fopen('php://output', 'w');

		    # write out the headers
		    fputcsv($fh, $columns);

		    # write out the data
		    foreach ( $data as $row ) {
		        fputcsv($fh, array_values($row));
		    }
		    fclose($fh);
		};
		return Response::stream($callback, 200, $headers);
	}
}