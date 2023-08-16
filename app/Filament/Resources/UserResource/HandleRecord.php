<?php

namespace App\Filament\Resources\UserResource;

use App\Filament\Traits;
use Illuminate\Database\Eloquent\Model;

trait HandleRecord
{
    use Traits\UseTransaction;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['permissions'] = collect($data['permissions'])->filter()->keys()->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove password key
        if (\array_key_exists('password', $data) && $data['password'] === null) {
            unset($data['password']);
        }

        if (\array_key_exists('permissions', $data)) {
            $data['permissions'] = collect($data['permissions'])->filter()->keys()->toArray();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Clear password field
        if (\array_key_exists('password', $this->data)) {
            unset($this->data['password']);
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        return $this->useTransaction(function () use ($data) {
            $user = static::getModel()::create($data);

            if (\array_key_exists('permissions', $data) && count($data['permissions']) > 0) {
                $user->syncPermissions($data['permissions']);
            }

            return $user;
        });
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return $this->useTransaction(function () use ($record, $data) {
            $record->update($data);

            // User can have zero permissions
            if (\array_key_exists('permissions', $data)) {
                $record->syncPermissions($data['permissions']);
            }

            return $record;
        });
    }
}
