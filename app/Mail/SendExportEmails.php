<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\RecurringExport;
use App\Services\ExportCSV;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class SendExportEmails extends Mailable
{
    use Queueable, SerializesModels;

    public $export;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(RecurringExport $export)
    {
        $this->export = $export;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $model = $this->export->model::getModel();
        $string = (new ExportCSV(
            $model::with(json_decode($this->export->with, true)),
            $this->export->params,
            $this->getType($this->export->model)
        ))->toString();

        $filename = $this->export->name.'-'
            .now()->format(settings()->get('application_date_format')).'.csv';

        // if(settings('email.global_bcc')) {
        //     $bcc[] = settings('email.global_bcc');
        // }

        $vars = [
            'name',
            'current_date'
        ];

        $d = [
            'name' => $this->export->name,
            'current_date' => now()
        ];

        $html = settings()->get('email_base_html');
        $css = settings()->get('email_stylesheet');

        $subject = parseSimpleTemplate(settings()
            ->get('recurring_export_email_subject'), $vars, $d);
        $message = parseSimpleTemplate(settings()
            ->get('recurring_export_email_template'), $vars, $d);

        return $this
            ->from(
                settings()->get('email_from_address'),
                settings()->get('email_from_name')
            )
            ->subject($subject)
            ->to($this->export->email_to)
            // ->bcc($bcc)
            ->html((new CssToInlineStyles)->convert(
                parseEmailHTML($html, $message),
                $css
            ))
            ->attachData($string, $filename, [
                'mime' => 'text/csv',
            ]);
    }

    protected function getType($model)
    {
        switch ($model) {
            case 'App\Contact\Contact':
                return 'contacts';
                break;

            case 'App\Organization\Organization':
                return 'organizations';
                break;

            case 'App\Item\Item':
                return 'items';
                break;

            case 'App\Lead\Lead':
                return 'leads';
                break;

            case 'App\Opportunities\Opportunities':
                return 'opportunities';
                break;

            case 'App\Proposal\Proposal':
                return 'proposals';
                break;

            case 'App\Contract\Contract':
                return 'contracts';
                break;

            case 'App\Project\Project':
                return 'projects';
                break;

            case 'App\Invoice\Invoice':
                return 'invoices';
                break;

            case 'App\Payment\Payment':
                return 'payments';
                break;

            case 'App\Expense\Expense':
                return 'expenses';
                break;

            case 'App\Vendor\Vendor':
                return 'vendors';
                break;
            default:
                abort(404, 'export type not found');
                break;
        }
    }
}
