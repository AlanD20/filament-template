<?php

namespace App\Filament\Resources\UserResource;

use App\Models\Permission;
use Illuminate\Validation\ValidationException;
use Filament\Tables\Columns\Contracts\Editable;

trait UpdateStatePermission
{
    public function updateTableColumnState(string $column, string $record, $input): mixed
    {
        $column = $this->getCachedTableColumn($column);

        if (! ($column instanceof Editable)) {
            return null;
        }

        $record = Permission::find($record);

        if (! $record) {
            return null;
        }

        $column->record($record);

        if ($column->isDisabled()) {
            return null;
        }

        try {
            $column->validate($input);
        } catch (ValidationException $exception) {
            return [
                'error' => $exception->getMessage(),
            ];
        }

        return $column->updateState($input);
    }
}
