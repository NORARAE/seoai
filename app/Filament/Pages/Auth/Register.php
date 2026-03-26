<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;

class Register extends BaseRegister
{
    /**
     * Inject defaults into the new user before it is created.
     * New registrants start unapproved with the 'buyer' role.
     */
    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['approved'] = false;
        $data['role']     = 'buyer';

        return $data;
    }
}
