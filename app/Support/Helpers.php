<?php

use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use App\Counter;

use App\Support\Settings;

function settings()
{
    return new Settings;
}

function to_json($response, $code = 200)
{
    return response()
        ->json($response, $code);
}

function counter($key)
{
    return Counter::where('key', $key)
        ->firstOrFail();
}


function formatDate($date, $format)
{
    if($date) {
        return Carbon\Carbon::parse($date)
            ->format($format);
    }

    return $date;
}

function formatCurrency($value, $currency)
{
    $code = $currency['code'];
    $precision = $currency['precision'];
    $decimal_separator = $currency['separator'];
    $placement = $currency['placement'];

    $money = number_format(
        $value, $precision, $decimal_separator,  ','
    );

    if($placement == 'before') {
        return $money = $code.''.$money;
    } else if($placement == 'after') {
        return $money = $money.''.$code;
    }

    return $money;
}

function formatMoney($value)
{
    $code = settings()->get('currency_code');
    $precision = settings()->get('currency_precision');
    $decimal_separator = settings()->get('decimal_separator');
    $placement = settings()->get('placement');

    $money = number_format(
        $value, $precision, $decimal_separator,  ','
    );

    if($placement) {
        if($placement == 'before') {
            return $code.' '.$money;
        }

        return $money.''.$money;
    }

    return $money;
}

function custom_fields($type, $values)
{
    $cf = App\CustomField::where('name', $type)
        ->firstOrFail();

    $values = json_decode($values, true);
    $fields = [];

    foreach($cf->fields as $field) {

        if(isset($values[$field['name']])) {
            $field['model'] = $values[$field['name']];
        }

        $fields[] = $field;
    }

    return $fields;
}

function custom_fields_value($type, $values)
{
    $cf = App\CustomField::where('name', $type)
        ->firstOrFail();

    $values = json_decode($values, true);
    $fields = [];

    foreach($cf->fields as $field) {

        if(isset($values[$field['name']])) {
            $fields[$field['name']] = $values[$field['name']];
        } else {
            $fields[$field['name']] = null;
        }
    }

    return $fields;
}

function custom_fields_preview($type, $values)
{
    $cf = App\CustomField::where('name', $type)
        ->firstOrFail();

    $values = json_decode($values, true);
    $fields = [];

    foreach($cf->fields as $field) {

        if(isset($values[$field['name']])) {
            $field['model'] = $values[$field['name']];
        } else {
            $field['model'] = null;
        }

        $fields[] = $field;
    }

    return $fields;
}

function custom_values($fields)
{
    $f = [];

    foreach($fields as $field) {
        $f[$field['name']] = $field['model'];
    }

    return $f;
}


if (! function_exists('lang')) {

    function lang()
    {
        $path = 'lang/'.config('app.locale').'.js';
        $manifestDirectory = '';
        static $manifests = [];
        if (! Str::startsWith($path, '/')) {
            $path = "/{$path}";
        }
        if ($manifestDirectory && ! Str::startsWith($manifestDirectory, '/')) {
            $manifestDirectory = "/{$manifestDirectory}";
        }
        if (file_exists(public_path($manifestDirectory.'/hot'))) {
            $url = file_get_contents(public_path($manifestDirectory.'/hot'));
            if (Str::startsWith($url, ['http://', 'https://'])) {
                return new HtmlString(Str::after($url, ':').$path);
            }
            return new HtmlString("//localhost:8080{$path}");
        }
        $manifestPath = public_path($manifestDirectory.'/lang-manifest.json');
        if (! isset($manifests[$manifestPath])) {
            if (! file_exists($manifestPath)) {
                throw new Exception('The Lang manifest does not exist.');
            }
            $manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
        }
        $manifest = $manifests[$manifestPath];
        if (! isset($manifest[$path])) {
            report(new Exception("Unable to locate Lang file: {$path}."));
            if (! app('config')->get('app.debug')) {
                return $path;
            }
        }
        return new HtmlString($manifestDirectory.$manifest[$path]);
    }
}

function purifier($html)
{
    $config = HTMLPurifier_Config::createDefault();
    // configuration goes here:
    $config->set('Core.Encoding', 'UTF-8'); // replace with your encoding
    $config->set('HTML.Doctype', 'XHTML 1.0 Transitional'); // replace with your doctype
    $purifier = new HTMLPurifier($config);
    // untrusted input HTML

    $pure_html = $purifier->purify($html);
    return $pure_html;
}

function parseEmailHTML($html, $partial)
{
    $vals = [
        'content' => $partial,
        'date' => now()->format('Y')
    ];

    $pattern = '/{+(.*?)}/';

    $replace = preg_replace_callback($pattern, function($match) use ($vals)
    {
        return isset($vals[$match[1]]) ? $vals[$match[1]] : $match[0];
    }, $html);
    return $replace;
}


function getStripeAmount($amount)
{
    $precision = settings()->get('currency_precision');
    $decimal_separator = settings()->get('decimal_separator');
    $placement = settings()->get('placement');

    $money = number_format(
        $amount, $precision, $decimal_separator,  ','
    );
    return str_replace('.', '', ($money));
}

function fromStripeAmount($amount)
{
    $l = strlen($amount);
    $precision = settings()->get('currency_precision');
    $p = $l - $precision;

    return substr_replace($amount, '.', $p, 0);
}


function extractImages($sections)
{
    $f = [];
    foreach($sections as $section) {
        foreach($section['fields'] as $field) {
            if($field['type'] == 'image') {
                if(!is_null($field['model'])) {
                    $f[] = $field['model'];
                }
            }
        }
    }
    return $f;
}


function custom_fields_names($type)
{
    $cf = App\CustomField::where('name', $type)
        ->firstOrFail();

    $fields = [];

    foreach($cf->fields as $field) {
        $fields[] = 'custom_fields.'.$field['name'];
    }

    return $fields;
}

function parseEmailTemplate($html, $available, $document, $type = null)
{
    $f = [];
    if($type) {
        $g = custom_fields_preview($type, $document->custom_values_2);

        foreach($g as $field) {
            $k = 'custom_fields.'.$field['name'];

            switch ($field['type']) {
                case 'text':
                case 'number':
                case 'textarea':
                case 'select':
                    $f[$k] = $field['model'];
                    break;
                case 'image':
                    $f[$k] = $field['model'];
                    break;

                case 'date':
                    $f[$k] = formatDate($field['model'], $field['format']);
                    break;
                case 'currency':
                    $f[$k] = formatCurrency($field['model'], $field['currency']);
                    break;

                case 'list':

                    break;

                case 'table':

                    break;
                default:
                    break;
            }
        }
    }


    // dd($f);
    $vals = array_merge(
        $f,
        global_variables(),
        array_dot($document->toArray())
    );

    // dd($content);
    $pattern = '/{+(.*?)}/';

    $replace = preg_replace_callback($pattern, function($match) use ($vals)
        {
            $filter = array_map('trim', explode('|', $match[1]));

            if(count($filter) > 1  && isset($vals[$filter[0]])) {
                if($filter[1] == 'formatMoney') {
                    return formatMoney($vals[$filter[0]]);
                }
                if($filter[1] == 'formatDate') {
                    return formatDate($vals[$filter[0]], settings()->get('application_date_format'));
                }
            } elseif(isset($vals[$match[1]])) {
                return $vals[$match[1]];
            }

            return $match[0];
        }, $html);

    return $replace;
}

function parseSimpleTemplate($html, $available, $vars)
{

    $vals = array_merge(global_variables(), $vars);

    $pattern = '/{+(.*?)}/';

    $replace = preg_replace_callback($pattern, function($match) use ($vals)
        {
            $filter = array_map('trim', explode('|', $match[1]));

            if(count($filter) > 1  && isset($vals[$filter[0]])) {
                if($filter[1] == 'formatMoney') {
                    return formatMoney($vals[$filter[0]]);
                }
                if($filter[1] == 'formatDate') {
                    return formatDate($vals[$filter[0]], settings()->get('application_date_format'));
                }
            } elseif(isset($vals[$match[1]])) {
                return $vals[$match[1]];
            }

            return $match[0];
        }, $html);

    return $replace;
}

function delete_first($message)
{
    return to_json([
        'message' => $message,
        'errors' => []
    ], 422);
}


function render_applied_invoices_table($p)
{
    $thead = [
        ['title' => 'Invoice', 'width' => '5'],
        ['title' => 'Issue Date', 'width' => '5'],
        ['title' => 'Due Date', 'width' => '5'],
        ['title' => 'Grand Total', 'width' => '5'],
        ['title' => 'Amount Applied', 'width' => '4'],
    ];
    $h = '<table class="items">';
    // 1. thead
    $h .= '<thead><tr>';
        foreach($thead as $col) {
            $h .= '<th class="w-'.$col['width'].'">'.$col['title'].'</th>';
        }
    $h .= '</tr></thead>';
    $h .= '<tbody>';
        foreach($p->lines as $line) {
            $h .= '<tr>';
            $h .= '<td>'.$line->invoice->number.'</td>';
            $h .= '<td>'.formatDate($line->invoice->issue_date, settings()->get('application_date_format')).'</td>';
            $h .= '<td>'.formatDate($line->invoice->due_date, settings()->get('application_date_format')).'</td>';
            $h .= '<td>'.formatMoney($line->invoice->grand_total).'</td>';
            $h .= '<td>'.formatMoney($line->amount_applied).'</td>';
            $h .= '</tr>';
        }
    $h .= '</tbody>';
    $h .= '</table>';
    return $h;
}

function custom_fields_to_array($type, $key, $values)
{
    $cvs = json_decode($values, true);

    $cf = App\CustomField::where('name', $type)
        ->firstOrFail();

    $f = [];
    foreach($cf->fields as $field) {
        $k = $key.'.custom_fields.'.$field['name'];
        if(isset($cvs[$field['name']])) {
            switch ($field['type']) {
                case 'date':
                    $f[$k] = formatDate($field['model'], $field['format']);
                    break;
                case 'currency':
                    $f[$k] = formatCurrency($field['model'], $field['currency']);
                    break;

                case 'list':

                    break;

                case 'table':

                    break;

                default:
                    $f[$k] = $cvs[$field['name']];
                    break;
            }
        }
    }
    return $f;
}

function global_fields()
{
    $cf = App\CustomField::where('name', 'globals')
        ->firstOrFail();

    $fields = [];

    foreach($cf->fields as $field) {
        $fields[] = 'globals.'.$field['name'];
    }

    return $fields;
}

function global_variables()
{
    $cf = App\CustomField::where('name', 'globals')
        ->firstOrFail();

    $f = [];
    foreach($cf->fields as $field) {
        $k = 'globals.'.$field['name'];
        switch ($field['type']) {
            case 'date':
                $f[$k] = formatDate($field['model'], $field['format']);
                break;
            case 'currency':
                $f[$k] = formatCurrency($field['model'], $field['currency']);
                break;

            case 'list':

                break;

            case 'table':

                break;

            default:
                $f[$k] = $field['model'];
                break;
        }
    }
    return $f;
}