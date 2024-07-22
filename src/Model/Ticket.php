<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Model;

use Carbon\Carbon;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\MorphMany;
use Hyperf\Database\Model\SoftDeletes;
use OnixSystemsPHP\HyperfCore\Model\AbstractModel;
use OnixSystemsPHP\HyperfFileUpload\Model\Behaviour\FileRelations;
use OnixSystemsPHP\HyperfSupport\Cast\CustomFieldCast;
use OnixSystemsPHP\HyperfSupport\Contract\SupportUserInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Ticket
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $source
 * @property array $custom_fields
 * @property int|null $created_by
 * @property int|null $modified_by
 * @property int|null $deleted_by
 * @property Carbon|null $completed_at
 * @property string|null $trello_id
 * @property string|null $trello_short_link
 * @property string|null $slack_id
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $ticket_title
 * @property string|null $trello_url
 * @property string|null $page_url
 * @property string $url
 * @property-read SupportUserInterface $creator
 * @property-read SupportUserInterface $editor
 * @property-read SupportUserInterface $archiver
 * @property-read MorphMany $files
 */
class Ticket extends AbstractModel
{
    use FileRelations;
    use SoftDeletes;

    public $fileRelations = [
        'files' => [
            'limit' => null,
            'required' => false,
            'mimeTypes' => ['*'],
            'presets' => [],
        ],
    ];

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'tickets';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'title',
        'content',
        'source',
        'custom_fields',
        'created_by',
        'modified_by',
        'deleted_by',
        'completed_at',
        'trello_id',
        'trello_short_link',
        'page_url',
        'slack_id',
        'files',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'custom_fields' => CustomFieldCast::class,
    ];

    /**
     * @return BelongsTo
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            $this->getContainer()->get(ConfigInterface::class)->get('support.app.user_model_class'),
            'created_by',
            'id',
        );
    }

    /**
     * @return BelongsTo
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(
            $this->getContainer()->get(ConfigInterface::class)->get('support.app.user_model_class'),
            'modified_by',
            'id',
        );
    }

    /**
     * @return BelongsTo
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function archiver(): BelongsTo
    {
        return $this->belongsTo(
            $this->getContainer()->get(ConfigInterface::class)->get('support.app.user_model_class'),
            'deleted_by',
            'id',
        );
    }

    /**
     * @return string
     */
    public function getTicketTitleAttribute(): string
    {
        $title = $this->title ? '- ' . $this->title : '';

        return "Ticket #$this->id $title";
    }

    /**
     * @return string|null
     */
    public function getTrelloUrlAttribute(): ?string
    {
        return $this->trello_short_link ? 'https://trello.com/c/' . $this->trello_short_link : null;
    }
}
