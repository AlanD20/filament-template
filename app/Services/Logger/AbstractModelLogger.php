<?php

namespace App\Services\Logger;

use Illuminate\Database\Eloquent\Model;
use Z3d0X\FilamentLogger\Loggers\AbstractModelLogger as BaseAbstractModelLogger;

abstract class AbstractModelLogger extends BaseAbstractModelLogger
{
    protected function getModelName(Model $model)
    {
        return str(class_basename($model))->snake();
    }

    protected function log(Model $model, string $event, string $description = null, mixed $attributes = null)
    {
        //* Formatting is done in 'ActivityResource' inside 'displayLogDescription' function

        if (is_null($description)) {
            $description = $this->getModelName($model) . ",{$event}";
        }

        if (auth()->check()) {
            $description = $this->getUserName(auth()->user()) . ",{$description}";
        }

        $this->activityLogger()
            ->event($event)
            ->performedOn($model)
            ->withProperties($attributes)
            ->log($description);
    }

    public function created(Model $model)
    {
        if ($model instanceof \BezhanSalleh\FilamentExceptions\Models\Exception) {
            return;
        }

        $this->log($model, 'created', attributes: $model->getAttributes());
    }

    public function updated(Model $model)
    {
        $changes = $model->getChanges();
        $data = [
            'old' => collect($model->getOriginal())->intersectByKeys($changes)->toArray(),
            'attributes' => $changes,
        ];

        //Ignore the changes to remember_token
        if (count($changes) === 1 && array_key_exists('remember_token', $changes)) {
            return;
        }

        $this->log($model, 'updated', attributes: $data);
    }

    public function deleted(Model $model)
    {
        $this->log($model, 'deleted');
    }
}
