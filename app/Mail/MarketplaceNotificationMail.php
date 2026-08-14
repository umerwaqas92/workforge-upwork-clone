<?php

namespace App\Mail;

use App\Models\Contract;
use App\Models\ContractMilestone;
use App\Models\JobPosting;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MarketplaceNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $greeting;
    public string $mainMessage;
    public ?string $actionUrl;
    public ?string $actionText;
    public ?array $details;

    public function __construct(
        string $subject,
        string $greeting,
        string $mainMessage,
        ?string $actionUrl = null,
        ?string $actionText = null,
        ?array $details = null
    ) {
        $this->emailSubject = $subject;
        $this->greeting = $greeting;
        $this->mainMessage = $mainMessage;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
        $this->details = $details;
    }

    public function build()
    {
        return $this->subject($this->emailSubject)
                    ->view('emails.notification');
    }
}
