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
     * Enables controller and set routes
     * @param Slim $app
     */
    public function enable(Slim $app)
    {
        $this->app = $app;
        $this->config = $this->app->config('invoice');

        // Routes
        $this->app
            ->get($this->config['prefix'] . '/rst', [$this, 'createRstAction'])
            ->name('invoice_rst');
        $this->app
            ->get($this->config['prefix'] . '/pdf', [$this, 'createPdfAction'])
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

        $tmpfile = tempnam('/tmp', 'dime-invoice');
        $rst = $tmpfile . '.rst';
        $pdf = $tmpfile . '.pdf';
        file_put_contents($rst, $this->app->view->fetch($this->template, $data));
        `rst2pdf $rst -o $pdf --stylesheets={$this->style}`;

        $this->app->response->headers->set('Content-Type', 'application/pdf');
        $this->app->response->write(file_get_contents($pdf));
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
        $templateName = $this->getTemplateName($this->app->request()->get('template'));
        $this->template = 'invoice/' . $templateName . '.rst.php';

        $templatePath = $this->app->view->getTemplatePathname($this->template);
        $this->style = dirname(realpath($templatePath)) . '/' . $templateName . '.style';
    }

    protected function _prepareData()
    {
        $this->app->response()->setStatus(200);

        $data = $this->app->request()->get('invoice');

        // data get extracted using php's extract method, which might be a good entry point for remote code execution...
        if (isset($data['templatePathname'])) {
            unset($data['templatePathname']);
        }
        if (!isset($data['currency'])) {
            $data['currency'] = '€';
        }

        return $data;
    }

}
