<?php

declare(strict_types=1);

namespace App\Filament\Resources\BorrowResource\Pages;

use App\Filament\Resources\BorrowResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBorrow extends CreateRecord
{
    protected static string $resource = BorrowResource::class;
}
