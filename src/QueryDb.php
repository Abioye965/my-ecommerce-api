<?php

class QueryDb
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function fetch($sql, $array = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($array);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row === false) {
                return [
                    'status' => 'failed',
                    'message' => 'Nothing was found!',
                ];
            }
            return $row;
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function insert($sql, $array = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($array);
            $last_id = $this->pdo->lastInsertId();

            return ['id' => $last_id, 'status' => 'success'];
        } catch (\PDOException $error) {
            return ['status' => 'error', 'message' => $error->getMessage()];
        }
    }

    public function update($sql, $array = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($array);
            $affectedRow = $stmt->rowCount();
            if ($affectedRow > 0) {
                return [
                    'status' => 'success',
                    'updated_row' => $affectedRow,
                ];
            } else {
                return [
                    'status' => 'failed',
                    'message' => 'Nothing was updated',
                ];
            }
        } catch (\PDOException $E) {
            return ['status' => 'error', 'message' => $E->getMessage()];
        }
    }

    public function delete($sql, $array = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($array);
            $deletedCount = $stmt->rowCount();

            if ($deletedCount > 0) {
                return [
                    'status' => 'success',
                    'message' => 'Record deleted successfully',
                ];
            } else {
                return [
                    'status' => 'failed',
                    'message' => 'Nothing was deleted',
                ];
            }
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
function getBody()
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (is_array($data)) {
        return $data;
    }
    // check if $_POST was polutated
    if (!empty($_POST)) {
        return $_POST;
    }
    // fallback for PUT/PATCH urlencoded bodies
    parse_str($raw, $data);
    return is_array($data) ? $data : [];
}
