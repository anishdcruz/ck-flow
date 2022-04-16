<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Template\Template;
use App\Template\Page;
use App\Services\TemplatePreview;
use App\Services\TemplateFields;
use DB;
use App\CustomField;
use Storage;
use Zipper;
use App\Image;
use Response;
use App\Contact\Contact;
use App\Organization\Organization;
use App\Opportunity\Opportunity;
use App\Proposal\Proposal;
use App\Invoice\Invoice;
use App\Payment\Payment;
use App\Payment\Deposit;
use App\Payment\Method;
use App\Expense\Expense;
use App\Contract\Contract;
use App\Vendor\Vendor;
use App\Services\TemplateParser;

class TemplateController extends Controller
{
    public function typeahead()
    {
        $results = Template::typeahead(['number', 'person', 'company']);

        return to_json([
            'results' => $results
        ]);

    }

    public function search()
    {
        $templates = Template::where('type_id', request()->get('type_id', 1))->search();

        $tempates = $templates->map(function($template) {
            $template->custom_fields = (new TemplateFields($template, []))->getFields();
            return $template;
        });

        return to_json([
            'collection' => $templates
        ]);
    }

    public function index()
    {
        $this->authorize('access', 'templates.index');
        return to_json([
            'collection' => Template::filter([
            	'id', 'name', 'page_size',
            	'created_at', 'type_id'
            ])
        ]);
    }

    public function create()
    {
        $this->authorize('access', 'templates.create');
        $form = [
            'name' => __('lang.untitled_template'),
            'type_id' => '1',
            'page_size' => 'A4',
            'orientation' => 'P',

            'header_height' => 0,
            'footer_height' => 0,

            'stylesheet' => '',

            'header_html' => '',
            'header_content_fields' => [
                ['title' => __('lang.default'), 'name' => 'default', 'fields' => []]
            ],

            'footer_html' => '',
            'footer_content_fields' => [
                ['title' => __('lang.default'), 'name' => 'default', 'fields' => []]
            ],
            'pages' => [
                [
                    'title' => __('lang.untitled_page'),
                    'name' => snake_case( __('lang.untitled_page')),
                    'orientation' => 'P',
                    'instruction' => '',
                    'index' => 0,
                    'page_html' => '',
                    'header_and_footer' => 1,
                    'content_fields' => [
                        ['title' => __('lang.default'), 'name' => 'default', 'fields' => []]
                    ],
                    'user_fields' => [
                        ['title' => __('lang.default'), 'name' => 'default', 'fields' => []]
                    ],
                ]
            ]
        ];

        $form = array_merge($form, (new template())->toArray());

        $item_variables = [
            'code', 'description', 'unit_price',
            'uom.name'
        ];


        return to_json([
            'form' => $form,
            'item_variables' => $item_variables
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('access', 'templates.create');
        $request->validate([
            'name' => 'required|unique:templates,name',
            'type_id' => 'required|in:1,2,3,4,5',
            'stylesheet' => 'required',

            'orientation' => 'required|in:L,P',
            'page_size' => 'required|alpha_dash',

            'header_height' => 'required|numeric|min:0',
            'footer_height' => 'required|numeric|min:0',

            'header_html' => 'required',
            'header_content_fields' => 'required',

            'footer_html' => 'required',
            'footer_content_fields' => 'required|array',

            'pages' => 'required|array|min:1',
            'pages.*.title' => 'required|distinct',
            'pages.*.name' => 'required|distinct',
            'pages.*.index' => 'required|integer|distinct',
            'pages.*.header_and_footer' => 'required|boolean',
            'pages.*.orientation' => 'required|in:L,P',
            'pages.*.instruction' => 'nullable',
            'pages.*.page_html' => 'required',
            'pages.*.content_fields' => 'required|array',
            'pages.*.user_fields' => 'required|array'
        ]);

        $t = new Template;
        $t->fill($request->except('pages', 'header_content_fields', 'footer_content_fields'));

        $t->header_content_fields = json_encode($request->header_content_fields);
        $t->footer_content_fields = json_encode($request->footer_content_fields);
        $t->save();

        $newPages = [];

        foreach($request->pages as $page) {
            $page['content_fields'] = json_encode($page['content_fields']);
            $page['user_fields'] = json_encode($page['user_fields']);
            $newPages[] = new Page($page);
        }

        $t->pages()->saveMany($newPages);

        return response()
            ->json([
                'saved' => true,
                'id' => $t->id
            ]);
    }

    public function show($id)
    {
        $this->authorize('access', 'templates.show');
        $template = Template::findOrFail($id, [
            	'id', 'name', 'page_size', 'orientation', 'header_height',
                'footer_height',
            	'created_at', 'type_id'
            ]);

        return to_json([
            'model' => $template
        ]);
    }

    public function edit($id)
    {
        $this->authorize('access', 'templates.update');
        $template = Template::with(['pages'])
        	->findOrFail($id);

        $hardCoded = [];

        // $cf = Cache::remember('cf_items', 3600, function() {
            $cf = CustomField::where('name', 'items')
                ->first();
        // });

        $customFields = [];

        if($cf) {
            $customFields = collect($cf->fields)->mapWithKeys(function($field) {
                $v = ['custom_fields.'.$field['name'] => $field['label']];
                return $v;
            })->toArray();
        }

        $item_variables = [
            'code' => __('lang.code'),
            'description' => __('lang.description'),
            'unit_price' => __('lang.unit_price'),
            'uom.name' => __('lang.uom.name')
        ];

        $c  = global_fields('globals');
        $templateVariables = [
            '1' => [
                'default' => Proposal::templateVariables(null),
                'globals' => $c,
                'contact' => Contact::templateVariables('contact'),
                'organization' => Organization::templateVariables('contact.organization'),
                'opportunity' => Opportunity::templateVariables('opportunity')
            ],
            '2' => [
                'default' => Contract::templateVariables(null),
                'globals' => $c,
                'contact' => Contact::templateVariables('contact'),
                'organization' => Organization::templateVariables('contact.organization'),
                'proposal' => Proposal::templateVariables('proposal')
            ],
            '3' => [
                'default' => Invoice::templateVariables(null),
                'globals' => $c,
                'contact' => Contact::templateVariables('contact'),
                'organization' => Organization::templateVariables('contact.organization'),
                'proposal' => Proposal::templateVariables('proposal'),
                'contract' => Contract::templateVariables('contract')
            ],
            '4' => [
                'default' => Payment::templateVariables(null),
                'globals' => $c,
                'contact' => Contact::templateVariables('contact'),
                'organization' => Organization::templateVariables('contact.organization')
            ],
            '5' => [
                'default' => Expense::templateVariables(null),
                'globals' => $c,
                'vendor' => Vendor::templateVariables('vendor')
            ]
        ];

        return to_json([
            'form' => $template,
            'item_variables' => array_merge($item_variables, $customFields),
            'template_variables' => $templateVariables
        ]);
    }

    public function update($id, Request $request)
    {
        $this->authorize('access', 'templates.update');
        $request->validate([
            'name' => 'required|unique:templates,name,'.$id,
            'type_id' => 'required|in:1,2,3,4,5',
            'stylesheet' => 'required',

            'orientation' => 'required|in:L,P',
            'page_size' => 'required|alpha_dash',

            'header_height' => 'required|numeric|min:0',
            'footer_height' => 'required|numeric|min:0',

            'header_html' => 'required',
            'header_content_fields' => 'required',

            'footer_html' => 'required',
            'footer_content_fields' => 'required|array',

            'pages' => 'required|array|min:1',
            'pages.*.id' => 'sometimes|required|integer', // todo: more checks
            'pages.*.title' => 'required|distinct',
            'pages.*.name' => 'required|distinct',
            'pages.*.index' => 'required|integer|distinct',
            'pages.*.header_and_footer' => 'required|boolean',
            'pages.*.orientation' => 'required|in:L,P',
            'pages.*.instruction' => 'nullable',
            'pages.*.page_html' => 'required',
            'pages.*.content_fields' => 'required|array',
            'pages.*.user_fields' => 'required|array'
        ]);

        $t = Template::findOrFail($id);
        $t->fill($request->except('pages', 'header_content_fields', 'footer_content_fields'));

        $t->header_content_fields = json_encode($request->header_content_fields);
        $t->footer_content_fields = json_encode($request->footer_content_fields);
        $t->save();

        $updatedIds = [];
        $newPages = [];

        foreach($request->pages as $page) {
            if(isset($page['id'])) {
                $found = $t->pages()
                    ->where('id', $page['id'])
                    ->first();

                if($found) {
                    $page['content_fields'] = json_encode($page['content_fields']);
                    $page['user_fields'] = json_encode($page['user_fields']);
                    $found->fill($page);

                    $found->save();
                    $updatedIds[] = $found->id;
                }
            } else {
                $page['content_fields'] = json_encode($page['content_fields']);
                $page['user_fields'] = json_encode($page['user_fields']);
                $newPages[] = new Page($page);
            }
        }

        $t->pages()->whereNotIn('id', $updatedIds)->delete();

        if(count($newPages)) {
            $t->pages()->saveMany($newPages);
        }

        return response()
            ->json(['saved' => true]);
    }

    // public function preview($id, TemplatePreview $p)
    // {
    //     $this->authorize('access', 'templates.show');
    //     $template = Template::with('pages')->findOrFail($id);

    //     return $p->load($template)
    //         ->output();
    // }

    public function preview($id, TemplateParser $p)
    {
        $this->authorize('access', 'templates.show');
        $template = Template::with('pages')->findOrFail($id);

        return $p->templatePreview($template);
    }

    public function export($id)
    {
        $this->authorize('access', 'templates.export');
        // 1. get template
        $t = Template::with('pages')->findOrFail($id);

        // 2. delete temp dir
        Storage::deleteDirectory('temp');

        // 3. css file
        Storage::put('temp/css/stylesheet.css', $t->stylesheet);

        // 4. header
        Storage::put('temp/header/content_fields.json', json_encode($t->header_content_fields, JSON_PRETTY_PRINT));
        Storage::put('temp/header/page.html', $t->header_html);

        // 4. footer
        Storage::put('temp/footer/content_fields.json', json_encode($t->footer_content_fields, JSON_PRETTY_PRINT));
        Storage::put('temp/footer/page.html', $t->footer_html);

        // 5. pages
        foreach($t->pages as $page) {
            Storage::put('temp/pages/'.$page->name.'/user_fields.json', json_encode($page->user_fields, JSON_PRETTY_PRINT));
            Storage::put('temp/pages/'.$page->name.'/content_fields.json', json_encode($page->content_fields, JSON_PRETTY_PRINT));
            Storage::put('temp/pages/'.$page->name.'/page.html', $page->page_html);
        }

        // 6. images
        $images = [];

        $images = array_merge($images, extractImages($t->header_content_fields));
        $images = array_merge($images, extractImages($t->footer_content_fields));

        foreach($t->pages as $page) {
            $images = array_merge($images, extractImages($page->content_fields));
            $images = array_merge($images, extractImages($page->user_fields));
        }

        foreach($images as $image) {
            Storage::copy($image, 'temp'.$image);
        }

        // manifest
        $manifest = [
            'type_id' => $t->type_id,
            'name' => $t->name,
            'page_size' => $t->page_size,
            'orientation' => $t->orientation,
            'header_height' => $t->header_height,
            'footer_height' => $t->footer_height,
            'pages' => $t->pages->map(function($page) {
                $p = [
                    'title' => $page->title,
                    'name' => $page->name,
                    'index' => $page->index,
                    'header_and_footer' => $page->header_and_footer,
                    'orientation' => $page->orientation,
                    'instruction' => $page->instruction
                ];

                return $p;
            }),
            'images' => $images
        ];

        Storage::put('temp/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));

        $n = $t->name.'.zip';
        $fileurl = storage_path('app/'.$n);

        Zipper::make($fileurl)
            ->add(storage_path('app/temp'))
            ->close();

        // download
        if (file_exists($fileurl)) {
            return Response::download($fileurl, $n, array('Content-Type: application/octet-stream','Content-Length: '. filesize($fileurl)))->deleteFileAfterSend(true);
        } else {
            return ['status'=>'zip file does not exist'];
        }
    }

    public function import(Request $request)
    {

        $this->authorize('access', 'templates.import');

        $request->validate([
            'template' => 'required|mimes:zip'
        ]);

        $upl = 'temp_upl.zip';
        // upload and store
        $request->file('template')
            ->storeAs('', $upl);

        $base = 'temp_upl';

        Zipper::make(storage_path('app/'.$upl))
            ->folder('')
            ->extractMatchingRegex(storage_path('app/'.$base), '/^(?!.*.php).*$/i');

        $t = new Template;

        $m = json_decode(Storage::get($base.'/manifest.json'));

        $found = Template::where('name', $m->name)->first();

        if($found) {
            $t->name = $m->name. ' '.str_random(3);
        } else {
            $t->name = $m->name;
        }

        $t->type_id = $m->type_id;
        $t->page_size = $m->page_size;
        $t->orientation = $m->orientation;
        $t->header_height = $m->header_height;
        $t->footer_height = $m->footer_height;

        // css
        $t->stylesheet = Storage::get($base.'/css/stylesheet.css');

        // header
        $t->header_content_fields = Storage::get($base.'/header/content_fields.json');
        $t->header_html = Storage::get($base.'/header/page.html');

        // footer
        $t->footer_content_fields = Storage::get($base.'/footer/content_fields.json');
        $t->footer_html = Storage::get($base.'/footer/page.html');

        // pages
        $pages = [];
        foreach($m->pages as $page) {

            $user = Storage::get($base.'/pages/'.$page->name.'/user_fields.json');
            $content = Storage::get($base.'/pages/'.$page->name.'/content_fields.json');
            $html = Storage::get($base.'/pages/'.$page->name.'/page.html');

            $pages[] = new Page([
                'content_fields' => $content,
                'user_fields' => $user,
                'page_html' => $html,

                'title' => $page->title,
                'name' => $page->name,
                'index' => $page->index,
                'header_and_footer' => $page->header_and_footer,
                'orientation' => $page->orientation,
                'instruction' => $page->instruction
            ]);
        }

        $t->save();

        $t->pages()->saveMany($pages);

        $i = 1;
        // images
        foreach($m->images as $image) {
            if(!Storage::exists($image)) {
                $size = Storage::size($base.$image);
                $extension = pathinfo(storage_path('app/'.$base.$image), PATHINFO_EXTENSION);
                list($width, $height) = getimagesize(storage_path('app/'.$base.$image));
                $dimension = "$width x $height";

                Image::create([
                    'title' => $t->name.' '.$i,
                    'filename' => $image,
                    'size' => $size,
                    'extension' => $extension,
                    'dimension' => $dimension
                ]);
                Storage::copy($base.$image, $image);
                $i++;
            }
        }

        Storage::delete($upl);
        Storage::deleteDirectory($base);

        return response()
            ->json([
                'saved' => true,
                'id' => $t->id
            ]);
    }

    public function destroy($id)
    {
        $this->authorize('access', 'templates.delete');
        $model = Template::findOrFail($id);

        if(DB::table('proposals')->where('template_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(DB::table('contracts')->where('template_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(DB::table('invoices')->where('template_id', $model->id)->count()) {
            return delete_first(__('lang.cannot_delete'));
        }

        // check settings
        if(settings()->check('default_proposal_template_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(settings()->check('default_contract_template_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(settings()->check('default_invoice_template_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(settings()->check('default_payment_template_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }

        if(settings()->check('default_expense_template_id', $model->id)) {
            return delete_first(__('lang.cannot_delete'));
        }


        $model->pages()->delete();
        $model->delete();

        return to_json([
            'deleted' => true
        ]);
    }
}
