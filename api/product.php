<?php
header('Content-Type: application/json');
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../src/QueryDb.php';

// if i want to send as a form body as well


// creating all products
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = getBody();

    //write sql query for insertion
    $sql = 'SELECT id FROM products WHERE name = ? LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$data['name']]);

    if ($stmt->rowCount() > 0) {
        // return error notification
        echo json_encode(['error' => 'This product already exist']);
    } else {
        //insert the product
        $dbq = new QueryDb($pdo);
        $sql =
            'INSERT INTO products (name, description, price, stock) VALUES (?,?,?,?)';
        // $stmt = $pdo->prepare($sql);
        $params = [
            $data['name'],
            $data['description'],
            $data['price'],
            $data['stock'],
        ];
        $response = $dbq->insert($sql, $params);
        if ($response['status'] == 'success') {
            $sql = 'SELECT * FROM products WHERE id = ?';
            $result = $dbq->fetch($sql, [$response['id']]);
            echo json_encode($result);
        } else {
            echo json_encode($response);
        }
    }
    exit();
}

// deleting a product using an id /////////////////////////
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Product id required',
        ]);
        exit();
    }
    $sql = 'SELECT id FROM products WHERE id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Cannot delete what does not exist']);
        exit();
    } else {
        $dbq = new QueryDb($pdo);
        $sql = 'DELETE FROM products WHERE id = ?';
        $response = $dbq->delete($sql, [$product['id']]);
        if ($response['status'] === 'success') {
            http_response_code(200);
        } elseif ($response['status'] === 'failed') {
            http_response_code(400);
        } else {
            http_response_code(500);
        }
        echo json_encode($response);
    }
    exit();
}

// updating a product with PUT//////////////////////////////////////////////////////////////
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Product id required',
        ]);
        exit();
    }

    $data = getBody();

    $sql = 'SELECT id FROM products WHERE id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Cannot update what does not exist']);
        exit();
    } else {
        $dbq = new QueryDb($pdo);
        $sql =
            'UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?';
        $params = [
            $data['name'],
            $data['description'],
            $data['price'],
            $data['stock'],
            $product['id'],
        ];

        $response = $dbq->update($sql, $params);
        if ($response['status'] === 'success') {
            $updated = $dbq->fetch('SELECT * FROM products WHERE id = ?', [
                $product['id'],
            ]);
            http_response_code(200);
            echo json_encode(['status' => 'success', 'data' => $updated]);
        } elseif ($response['status'] === 'failed') {
            http_response_code(400);
            echo json_encode($response);
        } else {
            http_response_code(500);
            echo json_encode($response);
        }
    }
    exit();
}

// ///////// updating a product with PATCH
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Product id required',
        ]);
        exit();
    }
    $data = getBody();
    $allowed = ['name', 'description', 'price', 'stock'];
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid JSON body',
        ]);
        exit();
    }
    $fields = array_intersect_key($data, array_flip($allowed));

    if (empty($fields)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'No valid fields to update',
        ]);
        exit();
    }
    $setParts = array_map(fn($col) => "$col = ?", array_keys($fields));
    $setClause = implode(', ', $setParts);
    $sql = 'UPDATE products SET $setClause WHERE id = ? ';
    $params = array_values($fields);
    $params[] = $_GET['id'];
    echo json_encode($fields);
    exit();
}

// trying to get a single product
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $products = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$products) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
        exit();
    }
    echo json_encode($products);
    exit();
}
// getting all products
$stmt = $pdo->query('SELECT * FROM products');
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($products);
