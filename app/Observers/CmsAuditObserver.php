<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Support\CmsContentRepository;
use Illuminate\Database\Eloquent\Model;

final class CmsAuditObserver
{
    public function created(Model $model): void
    {
        $this->record($model, 'created', [], $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']);

        if ($changes === []) {
            return;
        }

        $this->record($model, 'updated', array_intersect_key($model->getOriginal(), $changes), $changes);
    }

    public function deleted(Model $model): void
    {
        $this->record($model, 'deleted', $model->getOriginal(), []);
    }

    public function restored(Model $model): void
    {
        $this->record($model, 'restored', [], $model->getAttributes());
    }

    public function forceDeleted(Model $model): void
    {
        $this->record($model, 'force_deleted', $model->getOriginal(), []);
    }

    /**
     * @param  array<string, mixed>  $oldValues
     * @param  array<string, mixed>  $newValues
     */
    private function record(Model $model, string $event, array $oldValues, array $newValues): void
    {
        if ($model instanceof AuditLog) {
            return;
        }

        $request = app()->bound('request') ? request() : null;

        AuditLog::query()->create([
            'user_id' => auth()->id(),
            'event' => $event,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);

        app(CmsContentRepository::class)->forget();
    }
}
