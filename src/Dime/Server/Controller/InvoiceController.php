<?php

namespace Dime\Server\Controller;

use Dime\Server\Controller\SlimController;
use Slim\Slim;

/**
 * Controller to generate invoices
 */
class InvoiceController implements SlimController
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
        $this->config = $this->app->config('invoice');

        // Routes
        $this->app
            ->post($this->config['prefix'] . '/rst', [$this, 'createRstAction'])
            ->name('invoice_rst');
        $this->app
            ->post($this->config['prefix'] . '/pdf', [$this, 'createPdfAction'])
            ->name('invoice_pdf');
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
        $config = is_null($this->config) ? '' : ' --config=' . $this->config;
        $pdf = `echo "$rst" | rst2pdf -q --stylesheets={$this->style}$config`;

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

    protected function _prepareTemplate()
    {
        $doctype = $this->app->request()->get('doctype');
        if (is_null($doctype) || false === preg_match('/[a-zA-Z0-9_]/', $doctype)) {
            $this->app->response()->setStatus(400);
            die('invalid doctype');
        }

        if ($themes = $this->app->request()->get('themes')) {
            if (is_string($themes)) {
                $themes = explode(',', $themes);
            }
            $this->themes = array_merge($themes, $this->themes);
        }

        $this->template = $this->_findThemeFile($doctype, 'rst.php');
        $this->style    = $this->_findThemeFile($doctype, 'style');
        $this->config   = $this->_findThemeFile($doctype, 'config');

        if (is_null($this->template)) {
            $this->app->response()->setStatus(400);
            die('invalid type or template');
        }
    }

    protected function _findThemeFile($doctype, $filetype)
    {
        foreach ($this->themes as $theme) {
            if (is_null($theme) || false === preg_match('/[a-zA-Z0-9_]/', $theme)) {
                continue;
            }
            $path = "$doctype/$theme/$filetype";
            if (is_file($this->app->view->getTemplatePathname($path))) {
                return $path;
            }
        }
    }

    protected function _prepareData()
    {
        $data = json_decode($this->app->request()->getBody(), true);
        if (false === is_array($data)) {
            $this->app->response()->setStatus(400);
            die('invalid data');
        }

        $this->app->response()->setStatus(200);

        if (!isset($data['currency'])) {
            $data['currency'] = '€';
        }

        return $data;
    }

}
