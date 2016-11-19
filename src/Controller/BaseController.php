<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 11.06.16
 * Time: 11:52
 */

namespace MttProjecteuler\Controller;

use Carbon\Carbon;
use Silex\Application;

class BaseController
{
    /**
     * @param int $page
     * @param int $count
     * @param callable $urlGenerator
     *
     * @return array
     */
    public function getPaginationMetadata($page, $count, callable $urlGenerator)
    {
        return [
            'current' => $page,
            'last' => $count,
            'previous' => ($page > 1),
            'next' => ($page < $count),
            'url_generator' => $urlGenerator,
        ];
    }

    /**
     * @param Application $app
     * @param string $action
     * @param Carbon $lastModified
     * @param int $page
     *
     * @return string
     */
    public function computeEtag(Application $app, $action, Carbon $lastModified, $page = 1)
    {
        $username = 'anon';
        $user = $app['user'];
        if ($user) {
            $username = $user->getUsername();
        }

        $hash = sha1($app['git_hash'] . $action . $page . $username . $lastModified->format(\DateTime::RSS));

        return sprintf('%s-%s', substr($hash, 0, 8), substr($hash, -4));
    }

    /**
     * @param Application $app
     * @param string $action
     *
     * @return string
     */
    public function computeEtagStatic(Application $app, $action)
    {
        $username = 'anon';
        $user = $app['user'];
        if ($user) {
            $username = $user->getUsername();
        }

        $hash = sha1($app['git_hash'] . $action . $username);

        return sprintf('%s-%s', substr($hash, 0, 8), substr($hash, -4));
    }
}
