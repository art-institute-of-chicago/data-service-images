<?php

namespace App\Console\Commands;

use App\Behaviors\ImportsData;

use Aic\Hub\Foundation\AbstractCommand as BaseCommand;

abstract class AbstractCommand extends BaseCommand
{

    use ImportsData;

}


