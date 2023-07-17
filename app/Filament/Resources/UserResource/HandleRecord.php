<?php

namespace App\Filament\Resources\UserResource;

use App\Filament\Traits;
use Illuminate\Database\Eloquent\Model;

trait HandleRecord
{
    use Traits\HandleRecordTransaction;

    protected function afterSave(): void
    {
        // Clear password field
        if (\array_key_exists('password', $this->data)) {
            unset($this->data['password']);
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        return $this->handleRecord(function () use ($data) {
            $user = static::getModel()::create($data);

            if (\array_key_exists('permissions', $data) && count($data['permissions']) > 0) {
                $user->syncPermissions($data['permissions']);
            }

            $data['user_id'] = $user->id;

            return $user;
        });
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return $this->handleRecord(function () use ($record, $data) {
            $record->update($data);

            return $record;
        });
    }
}
