<?php
/**
 * Created by PhpStorm.
 * User: programmer
 * Date: 05/12/2017
 * Time: 19:56
 */

namespace Page\Backend\controllers;

use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use PDO;

class Subscribe extends Page
{
    function __construct($app)
    {
        parent::__construct($app);
    }

    function handle(Request $request) {

        if (!$this->is_admin) {
            throw new AccessDeniedException('Access denied');
        }

        if ($request->request->has('Update')) {
            $this->updateSubscribe($request);
            return  new RedirectResponse((($this->app['debug']) ? '/index_dev.php' : null).'/admin/subscribe/');
        }
        if ($request->request->has('Export')) {
            $filepath = $_SERVER['DOCUMENT_ROOT'].'/uplds/';

            $filename = $this->createCSV($filepath);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/octet-stream');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename. '"');
            $response->headers->set('Content-Length', filesize($filepath.$filename));
            $response->send();
        }

        return $this->showForm();
    }


    function showForm()
    {
        $sql="SELECT * FROM Subscribe";
        $stmt = $this->app['db']->query($sql);
        $links = $stmt->fetchAll();

        $content = $this->app['twig']->render('Page/Backend/templates/Subscribe.html.twig', array(
            'subscribes'  => $links
        ));

        return $this->formAction($content);

    }

    function updateSubscribe(Request $request)
    {
        if ($request->request->has("delete") && count($request->request->get("delete")) > 0) {
            foreach ($request->request->get("delete") as $key=>$value) {
                if ($value == 'yes') {
                    $sql = "DELETE FROM Subscribe WHERE id=:key";
                    $stmt = $this->app['db']->prepare($sql);
                    $stmt->bindValue(':key', $key, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
        }

    }

    private function createCSV($path) {
        $filename = 'export_'.date('d.m.Y').'.csv';
        $filepath = $path.$filename;

        $output = fopen($filepath, 'w+');

        $sql = "SELECT email FROM Subscribe WHERE is_active='yes'";
        $stmt = $this->app['db']->query($sql);
        $arr = $stmt->fetchAll();

        foreach ($arr as $value) {
            fputcsv($output, $value, ';');
        }

        fclose($output);

        return $filename;

    }
}