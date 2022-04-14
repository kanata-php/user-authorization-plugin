<?php

use League\Plates\Engine;
use Kanata\Annotations\Author;
use Kanata\Annotations\Plugin;
use UserAuthorization\Models\User;
use Kanata\Annotations\Description;
use Psr\Container\ContainerInterface;
use UserAuthorization\Services\AuthHelper;
use UserAuthorization\Services\Cookies;
use UserAuthorization\Services\SessionCookies;
use Illuminate\Database\Schema\Blueprint;
use UserAuthorization\Commands\SeedUsers;
use Kanata\Interfaces\KanataPluginInterface;
use UserAuthorization\Models\EmailConfirmation;
use Psr\Http\Message\ResponseInterface as Response;
use UserAuthorization\Http\Middlewares\AuthMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserAuthorization\Http\Controllers\LoginController;
use UserAuthorization\Http\Controllers\RegisterController;

/**
 * @Plugin(name="UserAuthorization")
 * @Description(value="Creates an HTTP Authorization Layer.")
 * @Author(name="Savio Resende",email="savio@savioresende.com")
 */

class UserAuthorization implements KanataPluginInterface
{
    const USER_AUTHORIZATION_VIEW = 'user-authorization';

    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return void
     */
    public function start(): void
    {
        if (is_http_execution()) {
            $this->register_middlewares();
            $this->local_views();
            $this->register_routes();
        }

        $this->register_migrations();
        $this->register_commands();
        $this->register_auth();
    }

    public function register_commands()
    {
        add_filter('commands', function($app) {
            $app->add(new SeedUsers());
            return $app;
        });
    }

    public function register_middlewares()
    {
        add_filter('http_middleware', function (Request $request) {
            return (new AuthMiddleware)($request);
        });
    }

    public function local_views()
    {
        add_filter('view_folders', function($view_folders){
            $view_folders['auth'] = __DIR__ . '/views';
            return $view_folders;
        });
    }

    public function register_routes()
    {
        add_filter('routes', function($app) {
            $app->get('/login', [LoginController::class, 'index'])->setName('login');
            $app->post('/login', [LoginController::class, 'loginHandler'])->setName('login-handler');
            $app->get('/logout', [LoginController::class, 'logoutHandler'])->setName('logout-handler');
            $app->get('/register', [RegisterController::class, 'index'])->setName('register');
            $app->post('/register', [RegisterController::class, 'registrationHandler'])->setName('register-handler');
            $app->get('/email-confirmation', [RegisterController::class, 'emailConfirmation'])->setName('email-confirmation');
            $app->get('/auth-message', [RegisterController::class, 'authMessage'])->setName('auth-message');
            return $app;
        });
    }

    public function register_migrations()
    {
        add_action('rollback_migrations', function () {
            // users
            if (mysql_table_exists(DB_DATABASE, User::TABLE_NAME)) {
                container()->db->schema()->drop(User::TABLE_NAME);
            }

            // email_confirmation
            if (mysql_table_exists(DB_DATABASE, EmailConfirmation::TABLE_NAME)) {
                container()->db->schema()->drop(EmailConfirmation::TABLE_NAME);
            }
        });

        add_action('migrations', function () {
            // users
            if (!mysql_table_exists(DB_DATABASE, User::TABLE_NAME)) {
                container()->db->schema()->create(User::TABLE_NAME, function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('name', 40);
                    $table->string('email', 80)->unique();
                    $table->string('password', 150);
                    $table->dateTime('email_verified_at')->nullable();
                    $table->timestamps();
                });
            }

            // email_confirmation
            if (!mysql_table_exists(DB_DATABASE, EmailConfirmation::TABLE_NAME)) {
                container()->db->schema()->create(EmailConfirmation::TABLE_NAME, function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('user_id', 40);
                    $table->string('token', 80);
                    $table->dateTime('expire_at');
                    $table->boolean('used')->default(false);
                    $table->timestamps();
                });
            }
        });
    }

    /**
     * This is an important hook to specify to views that the user is authorized
     * at the helper used for that by the views.
     */
    public function register_auth()
    {
        add_filter('is_logged', function (bool $is_logged, $request) {
            return AuthHelper::hasAuthSession($request);
        });
    }
}
