<?php

namespace UserAuthorization\Services;

use Swoole\Table;

class AuthTable
{
    private static $instance = null;

    protected Table $table;

    private function __construct()
    {
        $this->table = $this->startTable();
    }

    public static function getInstance(): AuthTable
    {
        if (self::$instance === null) {
            self::$instance = new AuthTable;
        }

        return self::$instance;
    }

    /**
     * @return Table
     */
    public function startTable(): Table
    {
        $table = new Table(1024);
        $table->column('user_id', Table::TYPE_INT, 10);
        $table->create();
        return $table;
    }

    /**
     * @param string $session_key
     * @param array $data.
     * @return void
     */
    public function store(string $session_key, array $data): void
    {
        $this->table->set($session_key, $data);
    }

    /**
     * @param string $session_key
     * @return ?array
     */
    public function get(string $session_key): ?array
    {
        $result = $this->table->get($session_key);
        return $result !== false ? $result : null;
    }

    /**
     * @param string $session_key
     * @return bool
     */
    public function delete(string $session_key): bool
    {
        return $this->table->del($session_key);
    }
}
