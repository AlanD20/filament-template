<?php

namespace App\Filament\Resources\UserResource;

use Illuminate\Support\Facades\Hash;

trait MutateFormData
{
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
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }
}
