<?php

use Kanata\Annotations\Author;
use Kanata\Annotations\Plugin;
use Slim\Routing\RouteCollectorProxy;
use Swoole\Table;
use Swoole\WebSocket\Server as WebSocketServer;
use Swoole\Http\Request as SwooleRequest;
use UserAuthorization\Commands\IssueToken;
use UserAuthorization\Http\Controllers\AdminController;
use UserAuthorization\Http\Controllers\Api\UsersController;
use UserAuthorization\Http\Middlewares\JwtAuthMiddleware;
use UserAuthorization\Models\Token;
use UserAuthorization\Models\User;
use Kanata\Annotations\Description;
use Psr\Container\ContainerInterface;
use Illuminate\Database\Schema\Blueprint;
use UserAuthorization\Commands\SeedUsers;
use Kanata\Interfaces\KanataPluginInterface;
use UserAuthorization\Models\EmailConfirmation;
use UserAuthorization\Http\Middlewares\AuthMiddleware;
use UserAuthorization\Http\Controllers\LoginController;
use UserAuthorization\Http\Controllers\RegisterController;
use UserAuthorization\Services\JwtTokenHelper;

/**
 * @Plugin(name="UserAuthorization")
 * @Description(value="Creates an HTTP Authorization Layer.")
 * @Author(name="Savio Resende",email="savio@savioresende.com")
 */

class UserAuthorization implements KanataPluginInterface
{
    const USER_AUTHORIZATION_VIEW = 'user-authorization';

    protected ContainerInterface $container;

    protected Table $socketAuthTable;

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
            $this->local_views();
            $this->register_routes();
        }

        $this->register_helpers();
        $this->register_migrations();
        $this->register_commands();
        $this->register_header_menu();

        // websockets
        $this->register_socket_auth_table();
        $this->register_websocket_hooks();
    }

    public function register_commands()
    {
        add_filter('commands', function($app) {
            $app->add(new SeedUsers());
            $app->add(new IssueToken());
            return $app;
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
            // login/logout
            $app->get('/login', [LoginController::class, 'index'])->setName('login');
            $app->post('/login', [LoginController::class, 'loginHandler'])->setName('login-handler');
            $app->get('/logout', [LoginController::class, 'logoutHandler'])->setName('logout-handler');

            // registration cycle
            $app->get('/register', [RegisterController::class, 'index'])->setName('register');
            $app->post('/register', [RegisterController::class, 'registrationHandler'])->setName('register-handler');
            $app->get('/email-confirmation', [RegisterController::class, 'emailConfirmation'])->setName('email-confirmation');

            // messages
            $app->get('/auth-message', [RegisterController::class, 'authMessage'])->setName('auth-message');

            // protected section

            // api management
            $app->group('/admin', function (RouteCollectorProxy $group) {
                $group->get('', [AdminController::class, 'index'])->setName('admin');
                $group->get('/api-tokens', [AdminController::class, 'apiTokens'])->setName('api-tokens');
                $group->post('/api-tokens', [AdminController::class, 'generateApiToken'])->setName('api-tokens-generate');
                $group->post('/api-tokens/delete', [AdminController::class, 'deleteApiToken'])->setName('api-tokens-delete');
            })->add(new AuthMiddleware);

            // api
            $app->group('/api', function (RouteCollectorProxy $group) {
                // $group->get('/users', [UsersController::class, 'index'])->setName('api-users-index');

                $group->get('/issue-single-use-token', [UsersController::class, 'generateSingleUserToken'])->setName('issue-single-user-token');
            })->add(new JwtAuthMiddleware);

            return $app;
        });
    }

    public function register_helpers()
    {
        add_filter('add_helpers', function (array $helpers) {
            $helpers[] = __DIR__ . '/helpers/auth-helpers.php';
            return $helpers;
        });
    }

    public function register_migrations()
    {
        add_action('rollback_migrations', function () {
            // email_confirmation
            if (mysql_table_exists(DB_DATABASE, EmailConfirmation::TABLE_NAME)) {
                container()->db->schema()->drop(EmailConfirmation::TABLE_NAME);
            }

            // tokens
            if (mysql_table_exists(DB_DATABASE, Token::TABLE_NAME)) {
                container()->db->schema()->drop(Token::TABLE_NAME);
            }

            // users
            if (mysql_table_exists(DB_DATABASE, User::TABLE_NAME)) {
                container()->db->schema()->drop(User::TABLE_NAME);
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
                    $table->foreignId('user_id');
                    $table->string('token', 80);
                    $table->dateTime('expire_at');
                    $table->boolean('used')->default(false);
                    $table->timestamps();
                });
            }

            // tokens
            if (!mysql_table_exists(DB_DATABASE, Token::TABLE_NAME)) {
                container()->db->schema()->create(Token::TABLE_NAME, function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('name', 40);
                    $table->string('token', 500);
                    $table->dateTime('expire_at')->nullable()->comment('Expire date for token. Null when doesnt expire.');
                    $table->string('aud', 100)->nullable()->comment('Audience: domain allowed to use token. Null when not restricted.');
                    $table->string('aud_protocol', 10)->nullable();
                    $table->integer('allowed_uses')->nullable()->comment('Number of times this token is allowed to be used.');
                    $table->integer('uses')->default(0)->comment('Number of times this token as been used.');
                    $table->foreignId('user_id');
                    $table->timestamps();
                });
            }
        });
    }

    public function register_header_menu()
    {
        add_filter('header_navbar_menus', function (array $menus) {
            $menus[] = 'auth::parts/navbar-auth';
            return $menus;
        });

        add_filter('header_navbar_menus_mobile', function (array $menus) {
            $menus[] = 'auth::parts/navbar-auth-mobile';
            return $menus;
        });
    }

    public function register_socket_auth_table()
    {
        $table = new Table(1024);
        $table->column('user_id', Table::TYPE_INT, 10);
        $table->create();
        $this->socketAuthTable = $table;
    }

    public function register_websocket_hooks()
    {
        add_action('socket_start_checkpoint', function(WebSocketServer $server, SwooleRequest $request) {
            if (!isset($request->get['token'])) {
                $this->deny_websocket_connection($server, $request->fd, 'Missing Token.');
                return;
            }

            $tokenRecord = Token::byToken($request->get['token'])->first();
            $tokenRecord->uses = $tokenRecord->uses + 1;
            $tokenRecord->save();

            if (null === $tokenRecord) {
                $this->deny_websocket_connection($server, $request->fd, 'Token doesn\'t exist.');
                return;
            }

            if (
                null !== $tokenRecord->aud
                && $tokenRecord->aud !== parse_url($request->header['origin'], PHP_URL_HOST)
            ) {
                $this->deny_websocket_connection($server, $request->fd, 'Origin not allowed by token.');
                return;
            }

            $decodedToken = JwtTokenHelper::decodeJwtToken($tokenRecord->token, $tokenRecord->name);

            $this->socketAuthTable->set($request->fd, ['user_id' => $decodedToken['user_id']]);
        });
    }

    private function deny_websocket_connection(WebSocketServer $server, int $fd, string $message)
    {
        logger()->debug('WS Connection Denied: ' . $message);
        $server->close($fd, true);
    }
}
