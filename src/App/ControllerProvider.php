<?php

namespace App;

use Silex\Api\ControllerProviderInterface;
use Silex\Application as App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Connection;

class ControllerProvider implements ControllerProviderInterface
{
    private $app;

    public function connect(App $app)
    {
        $this->app = $app;

        $app->error([$this, 'error']);

        $controllers = $app['controllers_factory'];

        $controllers
            ->get('/', [$this, 'homepage'])
            ->bind('homepage');


        $controllers
            ->match('/api/search/', function (Request $request) use($app) {

                $classFile = __DIR__."/../modules/API/tables/Table.php";
                if(file_exists ($classFile)) {
                    $classname = 'modules\\API\\tables\\Table';
                    $handler = new $classname($this->app);
                    return $handler->searchTable($request);
                }
                $app->abort(404, "Page not found");

            })
            ->method('GET')
            ;

        $controllers
            ->match('/api/letter/', function (Request $request) use($app) {

                $classFile = __DIR__."/../modules/API/tables/Table.php";
                if(file_exists ($classFile)) {
                    $classname = 'modules\\API\\tables\\Table';
                    $handler = new $classname($this->app);
                    return $handler->searchTableByFirstLetter($request);
                }
                $app->abort(404, "Page not found");

            })
            ->method('GET')
        ;


        $controllers
            ->match('/api/tables/{table_id}/', function ($table_id, Request $request) use($app) {

                if (!$table_id) $app->abort(404, "Page not found");

                /**
                 * @var $type string
                 * @var $id integer
                 */

                $classFile = __DIR__."/../modules/API/tables/Table.php";
                if(file_exists ($classFile)) {
                    $classname = 'modules\\API\\tables\\Table';
                    $handler = new $classname($this->app);
                    return $handler->showTable($table_id);
                }
                $app->abort(404, "Page not found");

            })
            ->method('GET')
            ->assert('event_id', '\d+')
        ;


        $controllers
            ->match('/api/tables/', function (Request $request) use($app) {

                $classFile = __DIR__."/../modules/API/tables/Table.php";
                if(file_exists ($classFile)) {
                    $classname = 'modules\\API\\tables\\Table';
                    $handler = new $classname($this->app);
                    return $handler->showAllTAbles($request);
                }
                $app->abort(404, "Page not found");

            })
            ->method('GET')
        ;



        return $controllers;
    }

    public function homepage(App $app)
    {
        $classname =  'modules\\FirstPage\\Frontend\\controllers\\FirstPage';
        $handler = new $classname(4, $app);
        return $handler->handle();
    }


    public function error(\Exception $e, Request $request, $code)
    {
        if ($this->app['debug']) {
            return;
        }

        switch ($code) {
            case 404:
                $message = 'The requested page could not be found.';
                break;
            default:
                $message = 'We are sorry, but something went terribly wrong.';
        }

        return new Response($message, $code);
    }

    function camelize($scored) {
        return lcfirst(
            implode(
                '',
                array_map(
                    'ucfirst',
                    array_map(
                        'strtolower',
                        explode(
                            '_', $scored)))));
    }
}
