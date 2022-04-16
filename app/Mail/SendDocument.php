<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Illuminate\Database\Eloquent\Model;
use App\Services\TemplateParser;
use App\Template\Template;

class SendDocument extends Mailable
{
    use Queueable, SerializesModels;

    protected $order;

    public $info;

    protected $type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Model $model, $info, $type)
    {
        $this->model = $model;
        $this->info = $info;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(TemplateParser $preview)
    {
        $html = settings()->get('email_base_html');
        $css = settings()->get('email_stylesheet');

        if($this->type == 'payment') {
            $template = Template::with('pages')
                ->findOrFail(settings()->get('default_payment_template_id'));
            $this->model->applied_invoices_table = render_applied_invoices_table($this->model);
        } else {
            $template = Template::with('pages')
                ->findOrFail($this->model->template_id);
        }

        $string = $preview->documentPreview($template, $this->model)
            ->toString();

        $filename = __('lang.'.$this->type).'-'.$this->model->number;

        $bcc = [];

        if($this->info['bcc']) {
            $bcc[] = $this->info['bcc'];
        }

        if($gbcc = settings()->get('global_bcc')) {
            $bcc[] = $gbcc;
        }

        return $this
            ->from(
                settings()->get('email_from_address'),
                settings()->get('email_from_name')
            )
            ->subject($this->info['subject'])
            ->to($this->info['email_to'])
            ->bcc($bcc)
            ->html((new CssToInlineStyles)->convert(
                parseEmailHTML($html, $this->info['message']),
                $css
            ))
            ->attachData($string, $filename, [
                'mime' => 'application/pdf',
            ]);
    }
}
