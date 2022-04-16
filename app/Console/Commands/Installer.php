<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CustomField;
use App\Organization\Category as OrgCat;
use App\Item\Uom;
use App\Item\Item;
use App\Item\Category as ItemCat;
use App\Lead\Status as LeadStatus;
use App\Opportunity\Category as OppCat;
use App\Opportunity\Source as OpSource;
use App\Opportunity\Stage as OpStage;
use App\Proposal\Status as PropStatus;
use App\Contract\Type as ConType;
use App\Contract\Status as ConStatus;
use App\Project\Stage as ProStage;
use App\Project\Category as ProCat;
use App\Invoice\Status as InvStatus;
use App\Payment\Deposit;
use App\Payment\Method;
use App\Expense\Category as ExpCat;
use App\Vendor\Category as VenCat;
use Storage;
use Zipper;
use App\Image;
use App\Template\Template;
use App\Template\Page;
use App\User;
use App\Role;
use App\Services\Permission;
use File;
use DB;

class Installer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flow:install {--demo} {--reset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup base application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if(!$this->option('reset')) {
            $this->call('key:generate', ['--force' => true]);
        }

        $this->call('migrate', ['--force' => true]);

        if($this->option('demo')) {
            $this->call('db:seed', ['--force' => true]);
        }
        $this->call('route:clear');
        $this->call('cache:clear');

        // custom field
        $this->setUpCustomFields();

        $this->setUpOrganization();

        $this->setUpItems();

        $this->setUpLeads();

        $this->setUpOpportunities();

        $this->setUpProposals();

        $this->setUpContracts();

        $this->setUpProjects();

        $this->setUpInvoices();

        $this->setUpPayments();

        $this->setUpExpenses();

        $this->setUpVendors();

        $this->setUpTemplates();

        $this->setUpUsers();

        $this->setUpEmail();

        $this->setUpWebPayments();

        $images = [
            '/images/tr6Mv9ohkvd9WnlSEkTqL79zHqhLuREH.png' => 'Product Image.png',
            '/images/Nx2RjCp00GG5tm6JpBMnSaPQ3aLJc2TH.png' => 'codekerala-logo-8080.png'
        ];

        foreach($images as $image => $label) {
            $base = base_path('database/temps');
            if(!Storage::exists($image)) {

                $size = File::size($base.$image);
                $extension = pathinfo($base.$image, PATHINFO_EXTENSION);
                list($width, $height) = getimagesize($base.$image);
                $dimension = "$width x $height";

                Image::create([
                    'title' => $label,
                    'filename' => $image,
                    'size' => $size,
                    'extension' => $extension,
                    'dimension' => $dimension
                ]);
                File::copy($base.$image, storage_path('app').$image);
            }
        }

        $globals = '[{"type":"text","label":"Company","name":"company","model":"Code Kerala","width":33},{"type":"textarea","label":"Address","name":"address","model":"2049 Myrl Ville Apt. 783\nHaileeland, WI 18411-4526","width":33},{"type":"image","label":"Logo","name":"logo","model":"\/images\/Nx2RjCp00GG5tm6JpBMnSaPQ3aLJc2TH.png","width":33}]';

        $item = CustomField::where('name', 'globals')
            ->firstOrFail();
        $item->fields = $globals;
        $item->save();

        if($this->option('demo')) {
            // demo
            // Nx2RjCp00GG5tm6JpBMnSaPQ3aLJc2TH.png

            $items = '[{"type":"image","label":"Photo","name":"photo","model":"\/images\/tr6Mv9ohkvd9WnlSEkTqL79zHqhLuREH.png","width":33}]';
            $item = CustomField::where('name', 'items')
                ->firstOrFail();
            $item->fields = $items;
            $item->save();

            DB::table('items')->update(['custom_values' => '{"photo":"\/images\/tr6Mv9ohkvd9WnlSEkTqL79zHqhLuREH.png"}']);
        }

        $this->info('Installation Successful!');
    }

    protected function setUpCustomFields()
    {
        $models = [
            [
                'name' => 'contacts',
                'fields' => '[]'
            ],
            [
                'name' => 'organizations',
                'fields' => '[]'
            ],
            [
                'name' => 'items',
                'fields' => '[]'
            ],
            [
                'name' => 'leads',
                'fields' => '[]'
            ],
            [
                'name' => 'opportunities',
                'fields' => '[]'
            ],
            [
                'name' => 'proposals',
                'fields' => '[]'
            ],
            [
                'name' => 'contracts',
                'fields' => '[]'
            ],
            [
                'name' => 'projects',
                'fields' => '[]'
            ],
            [
                'name' => 'invoices',
                'fields' => '[]'
            ],
            [
                'name' => 'payments',
                'fields' => '[]'
            ],
            [
                'name' => 'expenses',
                'fields' => '[]'
            ],
            [
                'name' => 'vendors',
                'fields' => '[]'
            ],
            [
                'name' => 'globals',
                'fields' => '[]'
            ]
        ];

        foreach($models as $model) {
            CustomField::create($model);
        }
    }

    protected function setUpOrganization()
    {
        $groups = [
            'Service',
            'Gov / Non-profit',
            'Education',
            'Small Business',
            'Healthcare'
        ];

        foreach($groups as $group) {
            $g = OrgCat::create(['name' => $group]);
        }

        settings()->set(
            'default_organization_category_id',
            OrgCat::value('id')
        );
    }

    protected function setUpItems()
    {
        $uoms = [
            'ea', 'set', 'point', 'pcs', 'cm',
            'meter', 'day(s)', 'hour(s)',
            'kg', 'km'
        ];

        foreach($uoms as $uom) {
            Uom::create(['name' => $uom]);
        }

        settings()->set(
            'default_item_uom_id',
            Uom::value('id')
        );

        $cats = ['Product', 'Service'];

        foreach($cats as $cat) {
            ItemCat::create(['name' => $cat]);
        }

        settings()->set(
            'default_item_category_id',
            ItemCat::value('id')
        );
    }

    protected function setUpLeads()
    {
        $statuses = [
            ['name' => 'New', 'color' => 'grey', 'locked' => 1],
            ['name' => 'Contacted', 'color' => 'blue'],
            ['name' => 'Attempted to Contact', 'color' => 'orange'],
            ['name' => 'Qualified', 'color' => 'green'],
            ['name' => 'Disqualified', 'color' => 'red']
        ];

        foreach($statuses as $status) {
            LeadStatus::create($status);
        }

        settings()->set(
            'lead_status_on_create_id',
            LeadStatus::where('name', 'New')->value('id')
        );
    }

    public function setUpOpportunities()
    {
        foreach([
            'Category A', 'Category B', 'Category C'
        ] as $c) {
            OppCat::create(['name' => $c]);
        }

        settings()->set(
            'default_opportunity_category_id',
            OppCat::value('id')
        );

        foreach([
            'Source A', 'Source B', 'Source C'
        ] as $c) {
            OpSource::create(['name' => $c]);
        }

        settings()->set(
            'default_opportunity_source_id',
            OpSource::value('id')
        );

        foreach([
            ['name' => 'New', 'color' => 'light_blue', 'locked' => 1],
            ['name' => 'Information Gathering', 'color' => 'light_green'],
            ['name' => 'Meeting', 'color' => 'pink'],
            ['name' => 'Proposal', 'color' => 'yellow'],
            ['name' => 'Follow up', 'color' => 'cyan'],
            ['name' => 'Closed', 'color' => 'red', 'locked' => 1]
        ] as $c) {
            OpStage::create($c);
        }

        settings()->set(
            'opportunity_stage_on_create_id',
            OpStage::where('name', 'New')->value('id')
        );

        settings()->set(
            'opportunity_stage_on_win_id',
            OpStage::where('name', 'Closed')->value('id')
        );

        settings()->set(
            'opportunity_stage_on_lost_id',
            OpStage::where('name', 'Closed')->value('id')
        );
    }

    public function setUpProposals()
    {
        foreach([
            ['name' => 'Draft', 'color' => 'grey', 'locked' => 1],
            ['name' => 'Sent', 'color' => 'light_green'],
            ['name' => 'Accepted', 'color' => 'blue'],
            ['name' => 'Declined', 'color' => 'red']
        ] as $c) {
            PropStatus::create($c);
        }

        settings()->set(
            'proposal_status_on_create_id',
            PropStatus::where('name', 'Draft')->value('id')
        );

        settings()->set(
            'proposal_status_on_email_sent_id',
            PropStatus::where('name', 'Sent')->value('id')
        );
    }

    protected function setUpContracts()
    {
        foreach([
            ['name' => 'Draft', 'color' => 'grey', 'locked' => 1],
            ['name' => 'Sent', 'color' => 'light_green'],
            ['name' => 'Accepted', 'color' => 'blue'],
            ['name' => 'Declined', 'color' => 'red'],
            ['name' => 'Terminated', 'color' => 'orange']
        ] as $c) {
            ConStatus::create($c);
        }

        settings()->set(
            'contract_status_on_create_id',
            ConStatus::where('name', 'Draft')->value('id')
        );

        settings()->set(
            'contract_status_on_email_sent_id',
            ConStatus::where('name', 'Sent')->value('id')
        );

        foreach([
            ['name' => 'Project'],
            ['name' => 'Annual Maintainance']
        ] as $c) {
            ConType::create($c);
        }

        settings()->set(
            'default_contract_type_id',
            ConType::value('id')
        );
    }

    protected function setUpProjects()
    {
        foreach([
            ['name' => 'Not Started', 'color' => 'light_blue', 'locked' => 1],
            ['name' => 'In Progress', 'color' => 'light_green'],
            ['name' => 'On Hold', 'color' => 'pink'],
            ['name' => 'Completed', 'color' => 'yellow']
        ] as $c) {
            ProStage::create($c);
        }

        settings()->set(
            'project_stage_on_create_id',
            ProStage::where('name', 'Not Started')->value('id')
        );

        foreach([
            'Category A', 'Category B', 'Category C'
        ] as $c) {
            ProCat::create(['name' => $c]);
        }

        settings()->set(
            'default_project_category_id',
            ProCat::value('id')
        );
    }

    protected function setUpInvoices()
    {
        foreach([
            ['name' => 'Draft', 'color' => 'grey', 'locked' => 1],
            ['name' => 'Sent', 'color' => 'light_green'],
            ['name' => 'Payment Requested', 'color' => 'pink', 'locked' => 1],
            ['name' => 'Paid', 'color' => 'blue', 'locked' => 1],
            ['name' => 'Partially Paid', 'color' => 'light_blue', 'locked' => 1],
            ['name' => 'Void', 'color' => 'red']
        ] as $c) {
            InvStatus::create($c);
        }

        settings()->set(
            'invoice_status_on_create_id',
            InvStatus::where('name', 'Draft')->value('id')
        );

        settings()->set(
            'invoice_status_on_email_sent_id',
            InvStatus::where('name', 'Sent')->value('id')
        );

        settings()->set(
            'invoice_status_on_payment_request_id',
            InvStatus::where('name', 'Payment Requested')->value('id')
        );

        settings()->set(
            'invoice_status_on_partial_payment_id',
            InvStatus::where('name', 'Partially Paid')->value('id')
        );

        settings()->set(
            'invoice_status_on_complete_payment_id',
            InvStatus::where('name', 'Paid')->value('id')
        );

        settings()->set(
            'receive_payment_on_status_ids',
            json_encode([
                InvStatus::where('name', 'Sent')->value('id'),
                InvStatus::where('name', 'Payment Requested')->value('id'),
                InvStatus::where('name', 'Partially Paid')->value('id')
            ])
        );
    }

    protected function setUpPayments()
    {
        foreach([
            ['name' => 'Cash'],
            ['name' => 'Cheque'],
            ['name' => 'Credit Card'],
            ['name' => 'Bank Transfer'],
            ['name' => 'Stripe'],
            ['name' => 'Paypal'],
            ['name' => 'Razorpay'],
        ] as $c) {
            Method::create($c);
        }

        settings()->set(
            'default_payment_method_id',
            Method::value('id')
        );

        foreach([
            ['name' => 'Undeposited Funds'],
            ['name' => 'Bank A'],
            ['name' => 'Bank B'],
            ['name' => 'Bank C'],
        ] as $c) {
            Deposit::create($c);
        }

        settings()->set(
            'default_payment_deposit_id',
            Deposit::value('id')
        );
    }

    protected function setUpExpenses()
    {
        $groups = [
            'Advertising',
            'Auto and Travel',
            'Cleaning',
            'Insurance',
            'Legal/Professional Fees',
            'Taxes',
            'Utilities',
            'Repair/Maintainance'
        ];

        foreach($groups as $group) {
            $g = ExpCat::create(['name' => $group]);
        }

        settings()->set(
            'default_expense_category_id',
            ExpCat::value('id')
        );
    }

    protected function setUpVendors()
    {
        $groups = [
            'Advertising',
            'Auto and Travel',
            'Cleaning',
            'Insurance',
            'Utilities',
            'Rent',
            'Repair/Maintainance',
            'Supplies'
        ];

        foreach($groups as $group) {
            $g = VenCat::create(['name' => $group]);
        }

        settings()->set(
            'default_vendor_category_id',
            VenCat::value('id')
        );
    }

    protected function setUpTemplates()
    {
        // load pre-exported template

        Storage::deleteDirectory('images');
        Storage::makeDirectory('images');
        // 1. proposal
        $id = $this->importTemplate('simple_quotation');

        // settings()->set(
        //     'default_proposal_template_id',
        //     $id
        // );

        // 2. contract
        $id = $this->importTemplate('simple_service_agreement');

        settings()->set(
            'default_contract_template_id',
            $id
        );

        // 3. invoice
        $id =$this->importTemplate('simple_invoice');

        settings()->set(
            'default_invoice_template_id',
            $id
        );

        // 4. payment
        $id =$this->importTemplate('simple_payment');

        settings()->set(
            'default_payment_template_id',
            $id
        );

        // 4. quote with image
        $id =$this->importTemplate('simple_quotation_with_image');



        // 5. expense (no need)
        $id = $this->importTemplate('simple_freelance');

        settings()->set(
            'default_proposal_template_id',
            $id
        );

    }

    protected function setUpUsers()
    {
        Role::create([
            'name' => 'Admin',
            'description' => 'Complete Access to flow',
            'permissions' => json_encode(Permission::schema())
        ]);

        $item = new User;
        $item->name = 'Administrator';
        $item->email = 'admin@flow.test';
        $item->role_id = Role::first()->id;
        $item->password = bcrypt('password');

        $item->save();
    }

    protected function setUpEmail()
    {
        $id = Method::where('name', 'Stripe')->value('id');
        settings()->set(
            'stripe_payment_method_id',
            $id
        );

        $id = Method::where('name', 'Razorpay')->value('id');
        settings()->set(
            'razorpay_payment_method_id',
            $id
        );

        $id = Method::where('name', 'Paypal')->value('id');
        settings()->set(
            'paypal_payment_method_id',
            $id
        );
    }

    protected function setUpWebPayments()
    {

    }

    protected function importTemplate($name)
    {
        $upl = base_path('database/temps/'.$name).'.zip';

        $base = $name;

        Zipper::make($upl)
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

        Storage::deleteDirectory($base);

        return $t->id;
    }
}
