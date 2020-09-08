<?php
namespace App\SegurosEnLinea\Sepomex\PostalCodes\Repositories;

use \DB;

class PostalCodeCreatorRepository
{
    const TABLE = 'codigos_postales';

    public function __invoke(array $data)
    {
        return DB::Table(self::TABLE)->insertGetId($data);
    }
}
