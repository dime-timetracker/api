<?php

namespace Dime\Server\Traits;

use Dime\Server\View\Json as JsonView;

/**
 * JsonTrait
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
trait Json
{
    /**
     * Render content and status
     *
     * @param mixed $data
     * @param int $status
     */
    protected function render($data, $status = 200)
    {
        $this->app->view(new JsonView());

        $this->app->response()->setStatus($status);

        $this->app->render('', $data);
    }
}
