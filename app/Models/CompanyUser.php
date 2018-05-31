<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

final class CompanyUser extends Pivot
{
    /** @var array */
    protected $casts = ['company_id' => 'string', 'user_id' => 'string'];
}
