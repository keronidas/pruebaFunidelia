<?php

$host = 'localhost';
$db = 'pruebafunidelia';
$user = 'root';
$pass = 'Diego92!';

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión a la base de datos exitosa.</br>";
    $stmt = $pdo->prepare("SELECT c.*, o.tracking_id, o.shipping_status, o.last_update 
                        FROM comments as c 
                        JOIN orders as o ON c.order_id = o.id 
                        WHERE c.name IS NULL AND c.email IS NULL AND c.comment IS NULL");
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $stmt = $pdo->prepare("SELECT o.id AS order_id FROM orders o JOIN comments c ON o.id = c.order_id WHERE c.name IS NULL AND c.email IS NULL AND c.comment IS NULL");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($orders)) {
        echo "No se encontraron pedidos con comentarios vacíos.</br>";
    } else {
        echo "Pedidos con comentarios vacíos encontrados: </br>";

        foreach ($orders as $order) {
            $orderId = $order['order_id'];
            echo "Procesando comentarios en pedido ID: $orderId</br>";


            $url = "https://jsonplaceholder.typicode.com/comments?postId=$orderId";
            $response = file_get_contents($url);

            if ($response === FALSE) {
                echo "Error de la API en: $orderId</br>";
                continue;
            }

            $comments = json_decode($response, true);

            foreach ($comments as $comment) {
                $name = $comment['name'];
                $email = $comment['email'];
                $commentText = $comment['body'];

                // Actualizar el primer comentario que encuentre
                $updateStmt = $pdo->prepare("UPDATE comments SET name = :name, email = :email, comment = :comment WHERE order_id = :order_id AND name IS NULL AND email IS NULL AND comment IS NULL LIMIT 1");
                $updateStmt->execute([
                    ':name' => $name,
                    ':email' => $email,
                    ':comment' => $commentText,
                    ':order_id' => $orderId
                ]);
            }
        }
    }
} catch (PDOException $e) {
    echo "Ha ocurrido un error en la conexión a la base de datos: " . $e->getMessage() . "</br>";
} catch (Exception $e) {
    echo "Ha ocurrido un error inesperado: " . $e->getMessage() . "</br>";
} finally {
    $pdo = null;
}
