<?php

namespace Dime\Server\Traits;

/**
 * Renderer
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
trait Renderer
{
    /**
     * Render content and status
     *
     * @param mixed $data
     * @param int $status
     */
    protected function render($data, $status = 200)
    {
        $this->app->render('', $data, $status);
    }
}
