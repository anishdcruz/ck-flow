<?php


$email_base_html = <<<EOD
<div class="content">
    {content}
</div>
EOD;

$email_stylesheet = <<<EOD
/* Based on The MailChimp Reset INLINE: Yes. */
body, body *:not(html):not(style):not(br):not(tr):not(code) {
    font-family: Avenir, Helvetica, sans-serif;
    box-sizing: border-box;
}
/* Client-specific Styles */
#outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;}
/* Prevent Webkit and Windows Mobile platforms from changing default font sizes.*/
.ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;}
/* Forces Hotmail to display normal line spacing.  More on that: http://www.emailonacid.com/forum/viewthread/43/ */
#backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}
/* End reset */
/* Some sensible defaults for images
Bring inline: Yes. */
img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic;}
a img {border:none;}
.image_fix {display:block;}
/* Yahoo paragraph fix
Bring inline: Yes. */
p {margin: 1em 0;}
/* Hotmail header color reset
Bring inline: Yes. */
h1, h2, h3, h4, h5, h6 {color: black !important;}
h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {color: blue !important;}
h1 a:active, h2 a:active,  h3 a:active, h4 a:active, h5 a:active, h6 a:active {
  color: red !important; /* Preferably not the same color as the normal header link color.  There is limited support for psuedo classes in email clients, this was added just for good measure. */
}
h1 a:visited, h2 a:visited,  h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited {
  color: purple !important; /* Preferably not the same color as the normal header link color. There is limited support for psuedo classes in email clients, this was added just for good measure. */
}
/* Outlook 07, 10 Padding issue fix
Bring inline: No.*/
table td {border-collapse: collapse;}
/* Remove spacing around Outlook 07, 10 tables
Bring inline: Yes */
table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
/* Styling your links has become much simpler with the new Yahoo.  In fact, it falls in line with the main credo of styling in email and make sure to bring your styles inline.  Your link colors will be uniform across clients when brought inline.
Bring inline: Yes. */
a {color: orange;}
/***************************************************
****************************************************
MOBILE TARGETING
****************************************************
***************************************************/
@media only screen and (max-device-width: 480px) {
  /* Part one of controlling phone number linking for mobile. */
  a[href^="tel"], a[href^="sms"] {
    text-decoration: none;
    color: blue; /* or whatever your want */
    pointer-events: none;
    cursor: default;
  }
  .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
    text-decoration: default;
    color: orange !important;
    pointer-events: auto;
    cursor: default;
  }
}
/* More Specific Targeting */
@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
  /* You guessed it, ipad (tablets, smaller screens, etc) */
  /* repeating for the ipad */
  a[href^="tel"], a[href^="sms"] {
    text-decoration: none;
    color: blue; /* or whatever your want */
    pointer-events: none;
    cursor: default;
  }
  .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
    text-decoration: default;
    color: orange !important;
    pointer-events: auto;
    cursor: default;
  }
}
EOD;

$proposal_email = <<<EOD
<p>Dear {contact.name},</p><p><br></p><p>Thank your for contacting us.</p><p>Please find the PDF attachment.</p><p>If you have any queries regarding this proposal, feel free to contact us.</p><p><br></p><p>Kind Regards,</p><p>Team {globals.company}</p>
EOD;

$contract_email = <<<EOD
<p>Dear {contact.name},</p><p><br></p><p>Please find the PDF attachment.&nbsp;</p><p>If you have any queries regarding this contract, feel free to contact us.</p><p><br></p><p>Kind Regards,</p><p>Team {globals.company}</p>
EOD;

$invoice_email = <<<EOD
<p>Dear {contact.name},</p><p><br></p><p>Please find the attached invoice. We appreciate your prompt payment.</p><p><br></p><p>Due Date: <strong>{due_date | formatDate}</strong></p><p><br></p><p>Amount: <strong>{grand_total | formatMoney}</strong></p><p><br></p><p>Let me know once the payment is completed.</p><p><br></p><p>Thank you</p><p>Team {globals.company}</p>
EOD;

$payment_email = <<<EOD
<p>Dear {contact.name},</p><p><br></p><p>Thank you for the payment. </p><p><br></p><p>Please find the attached payment Receipt.</p><p><br></p><p>Payment Amount: <strong>{amount_received | formatMoney}</strong></p><p>Payment Date: <strong>{payment_date | formatDate}</strong></p><p><br></p><p>If you have any queries regarding this payment receipt, feel free to contact us.</p><p><br></p><p>Thank you,</p><p>Team {globals.company}</p>
EOD;

$user_invite_email_template = <<<EOD
<p>Hi {name},</p><p><br></p><p>You have been invited to {globals.company}.&nbsp;</p><p>Go to the following url below and set your password to confirm your account.</p><p><br></p><p><a href="{register_url}" target="_blank">{register_url}</a></p><p><br></p><p>Thanks,</p><p>Team {globals.company}</p>
EOD;

$recurring_export_email_template = <<<EOD
<p>Hi there,</p><p><br></p><p>Your recurring export - <strong>{name}</strong> was successfully generated on<strong> {current_date}</strong></p><p><br></p><p>Thank you,</p><p><br></p><p><span class="ql-size-small">Note: This is a system generated email. Please do not reply to this email.</span></p>
EOD;

$payment_request_email_template = <<<EOD
<p>Dear {contact.name},</p><p><br></p><p>Please find the attached invoice. We appreciate your prompt payment.</p><p><br></p><p>Due Date: <strong>{invoice.due_date | formatDate}</strong></p><p>Due Amount: <strong>{invoice.grand_total | formatMoney}</strong></p><p><br></p><p>You can pay online by following this link:</p><p><br></p><p><a href="{payment_url}" target="_blank">{payment_url}</a></p><p><br></p><p>Let us know once the payment is completed.</p><p><br></p><p>Thank you,</p><p>Team {globals.company}</p>
EOD;

$payment_success_email = <<<EOD
<p>Dear {contact.name},</p><p><br></p><h1><strong>Thanks for the payment!</strong></h1><p><br></p><p>We have attached your receipt to this email.</p><p><br></p><p>Amount Received: {amount_received | formatMoney}</p><p>Reference: {reference}</p><p>Method: {method.name}</p><p><br></p><p>Thank you,</p><p>Team {globals.company}</p>
EOD;

$payment_received = <<<EOD
<p>Hi there,</p><p><br></p><p>New payment was received from web.</p><p><br></p><p>Payment Date: {payment_date | formatDate}</p><p>Contact: <strong>{contact.name}</strong></p><p>Amount Received: <strong>{amount_received | formatDate}</strong></p><p>Visit: <a href="{payment_page_url}" target="_blank">{payment_page_url}</a></p><p><br></p><p>Thank you,</p><p><br></p><p><span class="ql-size-small">Note: This is a system generated email. Please do not reply to this email.</span></p>
EOD;

return [
	'email_base_html' => $email_base_html,
	'email_stylesheet' => $email_stylesheet,
    'recurring_export_email_subject' => 'Recurring Export  - {name}',
    'recurring_export_email_template' => $recurring_export_email_template,
    'application_name' => 'Codekerala - Flow',
    'application_timezone' => 'UTC',
    'application_date_format' => 'd-M-Y',
    'currency_code' => 'USD',
    'currency_precision' => '2',
    'decimal_separator' => '.',
    'thousands_separator' => ',',
    'placement' => 'before',
    'email_from_address' => '',
    'email_from_name' => '',
    'global_bcc' => '',
    'default_organization_category_id' => null,
    'default_item_category_id' => null,
    'default_item_uom_id' => null,
    'lead_status_on_create_id' => null,
    'close_after_x_days' => null,
    'default_opportunity_category_id' => null,
    'default_opportunity_source_id' => null,
    'default_probability' => 50,
    'opportunity_stage_on_create_id' => null,
    'opportunity_stage_on_win_id' => null,
    'opportunity_stage_on_lost_id' => null,

    'proposal_status_on_create_id' => null,
    'proposal_status_on_email_sent_id' => null,
    'default_proposal_template_id' => null,
    'close_proposal_after_days' => null,

    'contract_status_on_create_id' => null,
    'contract_status_on_email_sent_id' => null,
    'default_contract_template_id' => null,
    'default_contract_type_id' => null,

    'project_stage_on_create_id' => null,
    'default_project_category_id' => null,

    'invoice_status_on_create_id' => null,
    'invoice_status_on_email_sent_id' => null,
    'invoice_status_on_partial_payment_id' => null,
    'invoice_status_on_complete_payment_id' => null,
    'invoice_status_on_payment_request_id' => null,
    'default_invoice_template_id' => null,
    'due_date_after_days' => null,
    'receive_payment_on_status_ids' => "[]", // json

    'default_payment_method_id' => null,
    'default_payment_deposit_id' => null,
    'default_payment_template_id' => null,

    'default_expense_category_id' => null,
    'default_expense_template_id' => null,

    'default_vendor_category_id' => null,

    'proposal_email_template' => $proposal_email,
    'proposal_email_subject' => 'Proposal {number} from {globals.company}',
    'contract_email_template' => $contract_email,
    'contract_email_subject' => 'Contract {number} from {globals.company}',
    'invoice_email_template' => $invoice_email,
    'invoice_email_subject' => 'Invoice {number} from {globals.company}',
    'payment_email_template' => $payment_email,
    'payment_email_subject' => 'Payment Receipt {number} from {globals.company}',

    'payment_request_email_template' => $payment_request_email_template,
    'payment_request_email_subject' => 'Payment Request {number} from {globals.company}',


    'enable_web_payment' => 0,
    'payment_notification_email' => null,
    'active_payment_gateway' => 'stripe',
    'payment_success_email' => $payment_success_email,
    'web_payment_email_subject' => 'Payment Receipt {number} from {globals.company}',
    'web_payment_notification_email_template' => $payment_received,
    'web_payment_notification_email_subject' => 'Payment Received {number} from {contact.name}',

    'user_invite_email_template' => $user_invite_email_template, // todo
    'user_invite_email_subject' => 'Invitation from {globals.company}',

    'enable_stripe' => 0,
    'stripe_publishable_key' => '<stripe_publishable_key>',
    'stripe_secret_key' => 'stripe_secret_key',
    'stripe_title' => 'Codekerala',
    'stripe_description' => 'Payment for Invoice',
    'stripe_logo_url' => null,
    'stripe_payment_method_id' => null,
    'stripe_payment_deposit_id' => null,

    'enable_razorpay' => 0,
    'razorpay_api_key' => '<razorpay_api_key>',
    'razorpay_secret_key' => '<razorpay_secret_key>',
    'razorpay_title' => 'Codekerala',
    'razorpay_description' => 'Payment for Invoice',
    'razorpay_logo_url' => null,
    'razorpay_payment_method_id' => null,
    'razorpay_payment_deposit_id' => null,

    //paypal
    'enable_paypal' => 0,
    'paypal_payment_deposit_id' => null,
    'paypal_payment_method_id' => null,
    'paypal_mode' => 'sandbox',
    'paypal_sandbox_client_id' => '<paypal_sandbox_client_id>',
    'paypal_sandbox_secret' => '<paypal_sandbox_secret>',
    'paypal_production_client_id' => '<paypal_production_client_id>',
    'paypal_production_secret' => '<paypal_production_secret>',
    'paypal_locale' => 'en_US',
    'paypal_size' => 'small',
    'paypal_color' => 'gold',
    'paypal_shape' => 'pill'
];