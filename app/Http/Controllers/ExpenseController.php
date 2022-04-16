<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expense\Expense;
use App\Services\ExportCSV;
use App\Project\Project;
use App\Vendor\Vendor;
use App\Expense\Category;
use App\Opportunity\Opportunity;
use DB;
use App\Services\TemplatePreview;
use App\Template\Template;

class ExpenseController extends Controller
{
    public function typeahead()
	{
	    $results = Expense::typeahead(['number']);

	    return to_json([
	        'results' => $results
	    ]);
	}

	public function search()
	{
	    return to_json([
	        'collection' => Expense::search()
	    ]);
	}

    public function index()
    {
        $this->authorize('access', 'expenses.index');
        $collection = Expense::with([
                'vendor:id,name', 'category:id,name'
            ])->when(request('opportunity_id'), function($query) {
                return $query->where('opportunity_id', request('opportunity_id'));
            })
            ->when(request('vendor_id'), function($query) {
                return $query->where('vendor_id', request('vendor_id'));
            })
            ->when(request('project_id'), function($query) {
                return $query->where('project_id', request('project_id'));
            })->filter();

        return to_json([
            'collection' => $collection
        ]);
    }

    public function export()
    {
        $this->authorize('access', 'expenses.export');
        return (new ExportCSV(
            Expense::with([
                'vendor', 'category', 'project', 'opportunity'
            ]), 'payments')
        )->download();
    }

    public function create(Request $request)
    {
        $this->authorize('access', 'expenses.create');
        $form = [
            'vendor' => null,
            'vendor_id' => null,
            'date' => date('Y-m-d'),
            'category' => null,
            'category_id' => null,
            'description' => '',
            'amount' => 0,
            'project' => null,
            'project_id' => null,
            'opportunity' => null,
            'opportunity_id' => null,
            'number' => __('lang.auto_generated'),
            'custom_fields' => custom_fields('expenses', "[]")
        ];

        if($request->has('project_id')) {
            $project = Project::findOrFail($request->project_id);

            $form['project_id'] = $project->id;
            $form['project'] = [
                'id' => $project->id,
                'number' => $project->number
            ];
        }

        if($request->has('opportunity_id')) {
            $f = Opportunity::findOrFail($request->opportunity_id);

            $form['opportunity_id'] = $f->id;
            $form['opportunity'] = [
                'id' => $f->id,
                'number' => $f->number
            ];
        }

        if(request()->has('vendor_id')) {
            $f = Vendor::findOrFail(request()->vendor_id);
            $form['vendor_id'] = $f->id;

            $form['vendor'] = [
                'name' => $f->name,
                'id' => $f->id
            ];
        }

        $id = settings()->get('default_expense_category_id');

        if($id) {
            $c = Category::findOrFail($id);
            $form['category'] = [
                'name' => $c->name,
                'id' => $c->id
            ];

            $form['category_id'] = $id;
        }

        return to_json([
            'form' => $form
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'expenses.create');
        $request->validate([
            'vendor_id' => 'required|integer|exists:vendors,id',
            'date' => 'required|date_format:Y-m-d',
            'category_id' => 'required|integer|exists:expense_categories,id',
            'description' => 'required',
            'amount' => 'required|numeric|min:0',
            'project_id' => 'nullable|integer|exists:projects,id',
            'opportunity_id' => 'nullable|integer|exists:opportunities,id',
            'custom_fields' => 'array'
        ]);

        $expense = new Expense;
        $expense->fill($request->all());
        $expense->custom_values = json_encode(custom_values($request->custom_fields));


        $expense = DB::transaction(function() use ($expense) {
            $c = counter('expense');
            $expense->number = $c->number;
            $expense->save();
            $c->increment('value');

            if($expense->project_id) {
                //  2. update project cost
                $project = $expense->project;
                $project->actual_cost = $project->actual_cost + $expense->amount;
                $percent = ($project->actual_cost / $project->estimated_cost) * 100;
                $project->cost_consumption = round($percent);
                $project->save();
            }

            //  2. update vendor revenue
            $vendor = $expense->vendor;
            $vendor->total_expense = $vendor->total_expense + $expense->amount;
            $vendor->save();

            return $expense;
        });

        return to_json([
            'saved' => true,
            'id' => $expense->id
        ]);
    }

    public function show($id)
    {
        $this->authorize('access', 'expenses.show');
        $exp = Expense::with([
        	'vendor:id,name', 'category:id,name', 'project:id,number', 'opportunity:id,number'
        	])
        	->findOrFail($id);

        $exp->custom_fields = custom_fields_preview('expenses', $exp->custom_values);

        return to_json([
            'model' => $exp,
        ]);
    }

    public function preview($id, TemplatePreview $preview)
    {
        $this->authorize('access', 'expenses.show');
        $p = Expense::with([
            'vendor', 'category:id,name', 'project', 'opportunity'
            ])
            ->findOrFail($id);

        $id = settings()->get('default_expense_template_id');

        if($id) {
            $template = Template::with('pages')
                ->findOrFail($id);

            return $preview->load($template)
                ->document($p)
                ->output();
        }

        abort(404, 'Expense Template Not Found!');
    }

    public function destroy($id)
    {
        $this->authorize('access', 'expenses.delete');
        $model = Expense::findOrFail($id);

        if($model->project_id) {
            //  2. update project cost
            $project = $model->project;
            $project->actual_cost = $project->actual_cost - $model->amount;
            $percent = ($project->actual_cost / $project->estimated_cost) * 100;
            $project->cost_consumption = round($percent);
            $project->save();
        }

        $vendor = $model->vendor;
        $vendor->total_expense = $vendor->total_expense - $model->amount;
        $vendor->save();

        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
