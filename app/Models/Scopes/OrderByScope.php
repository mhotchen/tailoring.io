<?php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class OrderByScope implements Scope
{
    /** @var string */
    private $column;

    /** @var string */
    private $direction;

    public function __construct(string $column, string $direction = 'asc')
    {
        $this->column = $column;
        $this->direction = $direction;
    }

    public function apply(Builder $builder, Model $model): void
    {
        $builder->orderBy($this->column, $this->direction);
    }
}
