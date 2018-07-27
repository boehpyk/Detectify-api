<?php

namespace modules\FirstPage\Frontend\controllers;

use App\Application;
use Symfony\Component\HttpFoundation\Request;
use Page\Frontend\controllers\Page;

class FirstPage extends Page
{
    protected $article_info;

    function __construct($article_id, Application $app)
    {
        parent::__construct($article_id, $app);
        $this->app = $app;
        $this->article_id = $article_id;
    }

    public function handle()
    {
        $content = $this->app['twig']->render('modules/FirstPage/Frontend/templates/FirstPage.html.twig', array(
            'article'       => $this->article_info,
        ));
        return $this->showAction($content);
    }


    public function getPageTitle()
    {
        return 'Orthorock.Ru';
    }
}