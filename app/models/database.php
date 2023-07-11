<?php

class databaseModel
{
    private static $instance = null;
    protected $pdo;

    private function __construct()
    {
        ['host' => $host, 'database' => $database, 'username' => $username, 'password' => $password, 'charset' => $charset] = System::getConfig('database');
        $dsn = "mysql:host=$host;dbname=$database;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new Exception('Failed to connect to the database: ' . $e->getMessage());
        }
    }
    private function queryCountAdd(){
        
        $count = $this->getQueryCount();
        if (!$count)
            $count = 1;
        else
            $count++;
        Registry::set('queryCount',$count);
    }
    public function getQueryCount(){
        return Registry::get('queryCount');
    }
    public function getExecutionTime(){
        return Registry::get('executionTime');
    }
    private function executionTimeAdd($time){
        $count = $this->getExecutionTime();
        if (!$count)
            $count = $time;
        else
            $count+=$time;
        Registry::set('executionTime',$count);
    }
    public static function getInstance(){
        if (!self::$instance)
            self::$instance = new databaseModel();
        return self::$instance;
    }
    public function getConnection(){
        return $this->pdo;
    }
    public function create($table, $data)
    {
        $this->queryCountAdd();
        $start = microtime(true);
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $statement = $this->pdo->prepare($sql);

        try {
            $statement->execute($data);
        } catch (PDOException $e) {
            throw new Exception('Failed to create record: ' . $e->getMessage());
        }
        $end = microtime(true);
        $this->executionTimeAdd($end - $start);
    }

    public function read($table, $columns = [], $conditions = [], $limit = null, $orderBy = null)
    {
        $this->queryCountAdd();
        $start = microtime(true);

        // Формирование части SELECT для выбранных столбцов
        $selectColumns = $columns ? implode(', ', $columns) : '*';
        $sql = "SELECT $selectColumns FROM `$table`";
        
        if (!empty($conditions)) {
            $whereConditions = implode(' AND ', array_map(function ($key) {
                return "`$key` = :$key";
            }, array_keys($conditions)));

            $sql .= " WHERE $whereConditions";
        }

        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        $statement = $this->pdo->prepare($sql);
        try {
            $statement->execute($conditions);
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Failed to retrieve records: ' . $e->getMessage());
        }
        $end = microtime(true);
        $this->executionTimeAdd($end - $start);
        return $results;
    }



    public function update($table, $data, $conditions)
    {
        $this->queryCountAdd();
        $start = microtime(true);

        // Создаем часть SQL-запроса для обновления значений
        $setValues = implode(', ', array_map(function ($key) {
            return "$key = :$key";
        }, array_keys($data)));

        // Создаем часть SQL-запроса для условий
        $whereConditions = implode(' AND ', array_map(function ($key) {
            return "$key = :$key";
        }, array_keys($conditions)));

        // Составляем полный SQL-запрос
        $sql = "UPDATE $table SET $setValues WHERE $whereConditions";
        
        // Подготавливаем запрос
        $statement = $this->pdo->prepare($sql);

        // Объединяем данные для обновления и условия в один массив параметров
        $parameters = array_merge($data, $conditions);
        try {
            // Выполняем запрос с передачей параметров
            $statement->execute($parameters);
        } catch (PDOException $e) {
            throw new Exception('Failed to update record: ' . $e->getMessage());
        }
        
        $end = microtime(true);
        $this->executionTimeAdd($end - $start);
    }

    public function delete($table, $conditions)
    {
        $this->queryCountAdd();
        $start = microtime(true);
        $whereConditions = implode(' AND ', array_map(function ($key) {
            return "$key = :$key";
        }, array_keys($conditions)));

        $sql = "DELETE FROM $table WHERE $whereConditions";
        $statement = $this->pdo->prepare($sql);

        try {
            $statement->execute($conditions);
        } catch (PDOException $e) {
            throw new Exception('Failed to delete record: ' . $e->getMessage());
        }
        $end = microtime(true);
        $this->executionTimeAdd($end - $start);
    }
    public function createTable($tableName, $columns) {
        $this->queryCountAdd();
        $start = microtime(true);

        $sql = "CREATE TABLE IF NOT EXISTS $tableName ($columns)";

        // Выполнение запроса
        $this->pdo->exec($sql);

        $end = microtime(true);
        $this->executionTimeAdd($end - $start);
    }

    public function tableExists($tableName) {
        $this->queryCountAdd();
        $start = microtime(true);

        $sql = "SELECT 1 FROM $tableName LIMIT 1";

        try {
            // Выполнение запроса
            $stmt = $this->pdo->query($sql);
            $stmt->fetch(PDO::FETCH_ASSOC);
            $end = microtime(true);
            $this->executionTimeAdd($end - $start);

            return true; // Таблица существует
        } catch (PDOException $e) {
            $end = microtime(true);
            $this->executionTimeAdd($end - $start);

            return false; // Таблица не существует или произошла ошибка
        }
    }
}
// // Создание экземпляра модели
// $model = new Model($pdo);

// // Чтение всех записей из таблицы "users"
// $allUsers = $model->read('users');

// // Чтение всех активных пользователей
// $activeUsers = $model->read('users', ['status' => 'active']);

// // Чтение пользователей с возрастом старше 25 лет, отсортированных по имени
// $filteredUsers = $model->read('users', ['age >' => 25], null, 'name ASC');

// // Чтение 10 активных пользователей, отсортированных по дате регистрации
// $limitedUsers = $model->read('users', ['status' => 'active'], 10, 'registration_date DESC');

// // Создание новой записи в таблице "products"
// $productData = [
//     'name' => 'Product Name',
//     'price' => 19.99,
//     'category' => 'Electronics'
// ];
// $model->create('products', $productData);

// // Обновление записи в таблице "users"
// $userId = 1;
// $newUserData = [
//     'name' => 'John Doe',
//     'email' => 'john@example.com'
// ];
// $model->update('users', $userId, $newUserData);

// // Удаление записи из таблицы "products"
// $productId = 3;
// $model->delete('products', $productId);
// Создание таблицы "users" (если она не существует)
// $tableName = 'users';
// $columns = 'id INT(11) AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255), email VARCHAR(255)';
// $database->createTable($tableName, $columns);

// // Проверка существования таблицы "users"
// $tableName = 'users';
// if ($database->tableExists($tableName)) {
//     echo "Таблица $tableName существует";
// } else {
//     echo "Таблица $tableName не существует";
// }
?>

