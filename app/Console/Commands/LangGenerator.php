<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;
use File;

class LangGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:js';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Translation files for Javascript App.';

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
        $langs = config('lang.selected');

        // create new lang dir
        Storage::disk('public_2')->deleteDirectory('lang');
        Storage::disk('public_2')->makeDirectory('lang');

        foreach($langs as $lang) {
            $minifiedCode = \JShrink\Minifier::minify($this->getContent($lang), array('flaggedComments' => false));
            Storage::disk('public_2')->put("lang/{$lang}.js", $minifiedCode);
        }

        $manifest = collect($langs)->mapWithKeys(function($lang) {
            return ["/lang/{$lang}.js" => "/lang/{$lang}.js?id=".str_random(16)];
        })->toArray();

        Storage::disk('public_2')->put("lang-manifest.json", json_encode($manifest, JSON_PRETTY_PRINT));

        $this->info('Lang Generated!');
    }

    protected function getContent($lang)
    {
        $name = config('lang.file');
        $file = require base_path("resources/lang/{$lang}/{$name}.php");
        $json = json_encode($file);

        $str = "
        (function () {
            FLOW_LANG = {$json};
        })();
        ";
        return $str;
    }
}
