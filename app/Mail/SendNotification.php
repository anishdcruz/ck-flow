<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Illuminate\Database\Eloquent\Model;
use App\Services\TemplatePreview;
use App\Template\Template;

class SendNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $order;

    public $info;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Model $model, $info)
    {
        $this->model = $model;
        $this->info = $info;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(TemplatePreview $preview)
    {
        $html = settings()->get('email_base_html');
        $css = settings()->get('email_stylesheet');

        return $this
            ->from(
                settings()->get('email_from_address'),
                settings()->get('email_from_name')
            )
            ->subject($this->info['subject'])
            ->to($this->info['email_to'])
            // ->bcc($bcc)
            ->html((new CssToInlineStyles)->convert(
                parseEmailHTML($html, $this->info['message']),
                $css
            ));
    }
}
