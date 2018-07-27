<?php

namespace modules\API\tables;

use App\Application;
use PDO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Util\Util;

class Table
{
    protected $app;
    private $inPage = 20;

    function __construct(Application $app)
    {
        $this->app = $app;
    }


    public function showAllTables(Request $request, $pageNumber = 1)
    {
        $sql = "SELECT 
                  *
                FROM 
                  TableIndex
                WHERE 
                  pageTitle != '' 
                ORDER BY 
                  pageTitle" .
                Util::getMySQLLimit($pageNumber, $this->inPage);

        $stmt = $this->app['db']->prepare($sql);
        $stmt->execute();
        $res = array();
//        while ($row = $stmt->fetch()) {
//            $res[] = $row["pageTitle"];
//        }
        return $this->app->json($stmt->fetchAll(), 201);

    }

    public function searchTable(Request $request)
    {
        $query = filter_var(urldecode($request->query->get('query')), FILTER_SANITIZE_STRING);
        $sql = "SELECT 
                  * 
                FROM
                  TableIndex
                WHERE 
                  pageTitle LIKE :query
                ";

        $stmt = $this->app['db']->prepare($sql);
        $stmt->bindValue('query', '%'.$query.'%', PDO::PARAM_INT);
        $stmt->execute();

        return $this->app->json($stmt->fetchAll(), 201);
    }


    public function searchTableByFirstLetter(Request $request)
    {
        $query = filter_var(urldecode($request->query->get('letter')), FILTER_SANITIZE_STRING);

        if (strlen($query) == 0) return;

        if ($query == 'other') {
            $sql = "SELECT 
                  * 
                FROM
                  TableIndex
                WHERE 
                  TRIM(BOTH '\"' FROM pageTitle) RLIKE '^[!#$%&()*+,\-./:;<=>?@[\\\]^`{|}~]+'
                ";

            $stmt = $this->app['db']->prepare($sql);
        }
        else {
            $sql = "SELECT 
                  * 
                FROM
                  TableIndex
                WHERE 
                  TRIM(BOTH '\"' FROM pageTitle) LIKE :query
                ";
            $stmt = $this->app['db']->prepare($sql);
            $stmt->bindValue('query', $query . '%', PDO::PARAM_STR);
        }
        $stmt->execute();

        return $this->app->json($stmt->fetchAll(), 201);
    }

    public function showTable($id)
    {
        $sql = "SELECT json FROM 
                  TableContent
                WHERE 
                  TableContent.id = :id
                LIMIT 1";

        $stmt = $this->app['db']->prepare($sql);
        $stmt->bindValue('id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll()[0]['json'];
    }




}