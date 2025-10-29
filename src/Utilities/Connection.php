<?php
namespace App\Utilities;

use PDO;
use PDOException;

class Connection
{
    private static ?PDO $pdo = null;

    public static function get(): PDO
    {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/../../config/database.php';

            $dsn = sprintf(
                '%s:host=%s;dbname=%s;port=%s;charset=%s',
                $config['driver'],
                $config['host'],
                $config['database'],
                $config['port'] ?? 3306,
                $config['charset'] ?? 'utf8mb4'
            );

            try {
                self::$pdo = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
            } catch (PDOException $e) {
                die('Database connection failed: ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
