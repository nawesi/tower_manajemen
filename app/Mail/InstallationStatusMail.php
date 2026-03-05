<?php

namespace App\Mail;

use App\Models\InstallationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InstallationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public InstallationRequest $req) {}

public function build()
{
    $status = strtoupper($this->req->status);

    return $this->from(config('mail.from.address'), config('mail.from.name'))
        ->subject("Status Pengajuan Pemasangan: {$status}")
        ->view('emails.installation-status');
}
}