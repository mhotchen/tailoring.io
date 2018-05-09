<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanyUser extends Pivot
{
    /** @var array */
    protected $casts = ['company_id' => 'string', 'user_id' => 'string'];
}
