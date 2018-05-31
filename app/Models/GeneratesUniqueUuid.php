<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @mixin Model
 */
trait GeneratesUniqueUuid
{
    /**
     * Generate a UUID and make sure it doesn't exist in the database already. According to my research it's extremely
     * unlikely that the same UUID will be generated twice, like we'd need billions or rows before the chance
     * really exists, but I don't like leaving things to chance.
     *
     * @param string $column
     * @return \Ramsey\Uuid\UuidInterface
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    private static function uniqueUuid(string $column = 'id'): UuidInterface
    {
        do {
            $id = Uuid::uuid4();
        } while ((new static)->where([$column => $id])->exists());

        return $id;
    }
}