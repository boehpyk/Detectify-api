<?php
/**
 * Created by PhpStorm.
 * User: programmer
 * Date: 21/11/2017
 * Time: 14:15
 */

namespace Page\Repository;

use PDO;


class SeminarSpeakerRepository
{
    var $data = null;

    function __construct($app, int $id = null)
    {
        $this->app = $app;
        if ($id) {
            $this->article_id = $id;
            $this->data = $this->getData();
        }
    }

    private function getData()
    {
        $sql = "SELECT * from SeminarSpeaker WHERE id=:id";
        $stmt = $this->app['db']->prepare($sql);
        $stmt->bindValue(':id', $this->article_id, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetch();
        $res['lastfirstname'] = $res['lastname'].' '.$res['firstname'];
        $res['firstlastname'] = $res['firstname'].' '.$res['lastname'];

        return $res;
    }

    /**
     * @return array
     */
    public function getAllSpeakers()
    {
        $sql = "SELECT * from SeminarSpeaker ORDER BY lastname";
        $stmt = $this->app['db']->query($sql);
        return $stmt->fetchAll();
    }

    public function getPageInfo()
    {
        return $this->data;
    }

    public function __get($name)
    {
        if ($this->data && array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        if ($this->data && $name == 'lastfirstname') {
            return $this->data['lastname'].' '.$this->data['firstname'];
        }
    }

}