<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PnHistory extends Model
{
    protected $table = 'pn_history';

    protected $fillable = [
        'type',
        'event',
        'timeline',
        'title',
        'message',
        'details',
        'created_at',
    ];

    protected $casts = [
        'type' => 'integer',
        'details' => 'array',
        'created_at' => 'datetime',
    ];

    // Event types
    const TYPE_SUCCESS = 1;
    const TYPE_ERROR = 2;
    const TYPE_WARNING = 3;
    const TYPE_INFO = 4;

    // Event identifiers for timeline
    const EVENT_WEBSTORE_SUSPENDED = 'webstore_suspended';
    const EVENT_APPLICATION_APPROVED = 'application_approved';
    const EVENT_APPLICATION_SUBMITTED = 'application_submitted';
    const EVENT_APPLICATION_REJECTED = 'application_rejected';
    const EVENT_MODERATION_IN_PROGRESS = 'moderation_in_progress';
    const EVENT_PAYOUT_ONBOARDING_COMPLETED = 'payout_onboarding_completed';
    const EVENT_ONBOARDING_COMPLETED = 'onboarding_completed';
    const EVENT_KYC_REQUIRED = 'kyc_required';
    const EVENT_KYC_COMPLETED = 'kyc_completed';
    const EVENT_ACTION_REQUIRED = 'action_required';
    const EVENT_WEBSTORE_RESTORED = 'webstore_restored';

    /**
     * Get events eligible for the timeline.
     */
    public static function getTimelineEvents()
    {
        return [
            self::EVENT_WEBSTORE_SUSPENDED,
            self::EVENT_APPLICATION_APPROVED,
            self::EVENT_APPLICATION_REJECTED,
            self::EVENT_APPLICATION_SUBMITTED,
            self::EVENT_MODERATION_IN_PROGRESS,
            self::EVENT_ONBOARDING_COMPLETED,
            self::EVENT_KYC_COMPLETED,
            self::EVENT_ACTION_REQUIRED,
            self::EVENT_PAYOUT_ONBOARDING_COMPLETED,
            self::EVENT_KYC_REQUIRED,
            self::EVENT_WEBSTORE_RESTORED,
        ];
    }

    /**
     * Scope to filter timeline events.
     */
    public function scopeTimeline($query)
    {
        return $query->where('timeline', true);
    }

    /**
     * Format the event for timeline display.
     */
    public function getTimelineFormat(): array
    {
        return [
            'event' => $this->event,
            'title' => $this->getEventTitle(),
            'date' => $this->created_at->format('jS F'),
            'time_ago' => $this->created_at->diffForHumans(),
            'message' => $this->message,
            'details' => $this->details,
        ];
    }

    /**
     * Get human-readable event title.
     */
    public function getEventTitle()
    {
        $titles = [
            self::EVENT_WEBSTORE_SUSPENDED => 'Webstore Suspended',
            self::EVENT_APPLICATION_APPROVED => 'Application Approved',
            self::EVENT_APPLICATION_SUBMITTED => 'Application Submitted',
            self::EVENT_APPLICATION_REJECTED => 'Application Rejected',
            self::EVENT_MODERATION_IN_PROGRESS => 'Moderation In Progress',
            self::EVENT_ONBOARDING_COMPLETED => 'Onboarding Completed',
            self::EVENT_KYC_REQUIRED => 'KYC Required',
            self::EVENT_KYC_COMPLETED => 'KYC Completed',
            self::EVENT_ACTION_REQUIRED => 'Action Required',
            self::EVENT_PAYOUT_ONBOARDING_COMPLETED => 'Payout Onboarding Completed',
            self::EVENT_WEBSTORE_RESTORED => 'Webstore Operational',
        ];

        return $titles[$this->event] ?? 'Unknown Event';
    }
}
