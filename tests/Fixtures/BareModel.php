<?php

declare(strict_types=1);

namespace Opscale\Validations\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

final class BareModel extends Model
{
    public $timestamps = false;

    protected $table = 'bare_models';

    protected $guarded = [];
}
