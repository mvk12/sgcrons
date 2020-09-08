<?php
namespace App\SegurosEnLinea\Sepomex\PostalCodes\Repositories;

use \DB;

class TruncatePostalCodesRepository
{
    const TABLE = 'codigos_postales';

    public function __invoke()
    {
        DB::table(self::TABLE)->truncate();
    }
}
