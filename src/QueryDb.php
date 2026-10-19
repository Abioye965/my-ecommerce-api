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
