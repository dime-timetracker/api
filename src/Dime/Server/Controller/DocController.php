<?php

namespace Dime\Server\Controller;

use Dime\Server\Controller\SlimController;
use Slim\Slim;

/**
 * Controller to generate rst files and PDFs
 */
class DocController implements SlimController
{

    /**
     * @var Slim
     */
    protected $app;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $doctype;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var string
     */
    protected $style;

    /**
     * @var array
     */
    protected $themes = ['default', 'base'];

    /**
     * Enables controller and set routes
     * @param Slim $app
     */
    public function enable(Slim $app)
    {
        $this->app = $app;
        $this->config = $this->app->config('doc');

        // Routes
        $this->app
            ->get($this->config['prefix'] . '/rst', [$this, 'createRstAction']);
        $this->app
            ->get($this->config['prefix'] . '/pdf', [$this, 'createPdfAction']);
        $this->app
            ->post($this->config['prefix'] . '/rst', [$this, 'createRstAction'])
            ->name('doc_rst');
        $this->app
            ->post($this->config['prefix'] . '/pdf', [$this, 'createPdfAction'])
            ->name('doc_pdf');
    }

    /**
     * [POST] /rst
     */
    public function createRstAction()
    {
        $data = $this->_prepareTemplate();
        $data = $this->_prepareData();
        $this->app->response->headers->set('Content-Type', 'text/plain');
        $this->app->render($this->template, $data);
    }

    /**
     * [POST] /pdf
     */
    public function createPdfAction()
    {
        $data = $this->_prepareTemplate();
        $data = $this->_prepareData();

        $rst = addcslashes($this->app->view->fetch($this->template, $data), '"$`');
        $params = '';
        if (false === is_null($this->config)) {
            $params .= ' --config=' . realpath($this->config);
        }
        if (false === is_null($this->style)) {
            $params .= ' --stylesheets=' . realpath($this->style);
        }
        $pdf = `echo "$rst" | rst2pdf -q$params`;

        $this->app->response->headers->set('Content-Type', 'application/pdf');
        $this->app->response->write($pdf);
    }
    
    protected function getStylePath() {
    }

    protected function getTemplateName($template)
    {
        $template = $this->app->request()->get('template');
        if (is_null($template)) {
            $template = 'default';
        }
        if (false === preg_match('/[a-zA-Z0-9_]+/', $template)) {
            return null;
        }
        return $template;
    }

    protected function _getDoctype()
    {
        if (is_null($this->doctype)) {
            $this->doctype = $this->app->request()->get('doctype');
            if (is_null($this->doctype) || false === preg_match('/[a-zA-Z0-9_]/', $this->doctype)) {
                $this->app->response()->setStatus(400);
                die('invalid doctype');
            }
        }

        return $this->doctype;
    }

    protected function _prepareTemplate()
    {
        if ($themes = $this->app->request()->get('themes')) {
            if (is_string($themes)) {
                $themes = explode(',', $themes);
            }
            $this->themes = array_merge($themes, $this->themes);
        }

        $this->template = $this->_findThemeFile('rst.php');
        $this->style    = $this->_findThemeFile('style');
        $this->config   = $this->_findThemeFile('config');

        if (is_null($this->template)) {
            $this->app->response()->setStatus(400);
            die('invalid type or template');
        }
    }

    protected function _findThemeFile($filetype)
    {
        $doctype = $this->_getDoctype();

        foreach ($this->themes as $theme) {
            if (is_null($theme) || false === preg_match('/[a-zA-Z0-9_]/', $theme)) {
                continue;
            }
            $path = $this->app->view->getTemplatePathname("$doctype/$theme/$filetype");
            if (is_file($path)) {
                if ('rst.php' === $filetype) {
                    return "$doctype/$theme/$filetype";
                }
                return $path;
            }
        }
    }

    protected function _prepareData()
    {
        $data = json_decode($this->app->request()->getBody(), true);
        if (false === is_array($data)) {
            //$this->app->response()->setStatus(400);
            //die('invalid data');
            $data=[];
        }

        $this->app->response()->setStatus(200);

        if (isset($data['logo'])) {
            $logoFilename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $data['logo']);
            if ($logoFilename) {
                $logoPath = $this->_findThemeFile($data['logo']);
            }
            if (isset($logoPath) && strlen($logoPath)) {
                $data['logo'] = $logoPath;
            } else {
                unset($data['logo']);
            }
        }

        if (!isset($data['currency'])) {
            $data['currency'] = '€';
        }

        return $data;
    }

}
