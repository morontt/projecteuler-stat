<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 02.06.16
 * Time: 22:05
 */

namespace MttProjecteuler\Command;

use Silex\Application;
use Symfony\Component\Console\Command\Command;

class BaseCommand extends Command
{
    /**
     * @var Application
     */
    protected $container;

    /**
     * @param Application $container
     *
     * @return $this
     */
    public function setContainer(Application $container)
    {
        $this->container = $container;

        return $this;
    }
}
