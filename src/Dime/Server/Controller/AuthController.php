<?php

namespace Dime\Server\Controller;

use Dime\Server\Controller\SlimController;
use Dime\Server\Hash\SymfonySecurityHasher as Hasher;
use Dime\Server\Middleware\Authorization;
use Dime\Server\Middleware\Route;
use Dime\Server\Middleware\ContentType;
use Dime\Server\Model\Access;
use Dime\Server\Model\User;
use Dime\Server\View\Json as JsonView;
use Slim\Slim;

/**
 * AuthController
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class AuthController implements SlimController
{

    /**
     * @var Slim
     */
    protected $app;

    /**
     * @var Hasher
     */
    protected $hasher;
    

    public function enable(Slim $app)
    {
        $this->app = $app;
        $this->hasher = new Hasher();
        $this->app->add(new Route('/login', new ContentType()));
        $this->app->post('/login', [$this, 'loginAction']);

        $this->app->add(new Route('/logout', new Authorization($this->app->config('auth'))));
        $this->app->add(new Route('/logout', new ContentType()));
        $this->app->post('/logout', [$this, 'logoutAction']);
    }

    /**
     * Create an access token and send it back.
     *
     * [POST] /login
     *
     * {
     *   username: USERNAME
     *   client: CLIENTID
     *   password: PASSWORD
     * }
     */
    public function loginAction()
    {
        $input = filter_var_array($this->app->request->getBody(), [
            'username' => FILTER_SANITIZE_STRING,
            'client' => FILTER_SANITIZE_STRING,
            'password' => FILTER_SANITIZE_STRING
        ]);

        if (!empty($input['username']) && !empty($input['client']) && !empty($input['password'])) {
            try {
                $user = User::where('username', $input['username'])->firstOrFail();
                if ($this->authenticate($user, $input['password'])) {
                    $access = Access::firstOrNew([ 'user_id' => $user->id, 'client' => $input['client'] ]);
                    $access->token =  $this->hasher->make(uniqid($input['username'] . $input['client'] . microtime(), true));
                    $access->save();
                    $this->render([ 'token' => $access->token ]);
                }
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
                $this->render([ 'error' => 'Authentication error' ], 404);
            }
        } else {
            $this->render([ 'error' => 'Authentication error' ], 404);
        }
    }

    /**
     * Remove access token.
     *
     * [POST] /logout
     *
     * 
     */
    public function logoutAction()
    {
        if ($this->app->access) {
            $this->app->access->delete();
        }
    }

    /**
     * Authenticate user with password.
     * 
     * @param User $user
     * @param string $password
     * @return boolean
     */
    protected function authenticate(User $user, $password) {
        return !empty($user)
            && $this->hasher->check($password, $user->password, ['salt' => $user->salt]);
    }

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
