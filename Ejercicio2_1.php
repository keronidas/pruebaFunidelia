<?php
session_start();

function addToCart($productId, $quantity)
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['product_id']) ? $_POST['product_id'] : null;
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : null;

    if ($productId === null || !is_numeric($productId)) {
        echo json_encode(["error" => "El producto no puede ser nulo"]);
        return;
    }
    
    if ($quantity === null || !is_numeric($quantity) || intval($quantity) <= 0) {
        echo json_encode(["error" => "La cantidad tiene que ser mayor de cero"]);
        return;
    }

    addToCart(intval($productId), intval($quantity));

    echo json_encode(["success" => "Producto agregado al carrito correctamente."]);
} else {
    echo json_encode(["error" => "Error a la hora de hacer la peticion"]);
}




/*
Observaciones:

Suponiendo que el productId tenga un valor numerico
1. No se valida que los datos recibidos sean válidos, habria que comprobar que el productId y la cantidad tengan un valor numerico y comprobar que no sea nulo.


Podria usarse 

if (!is_numeric($productId) || !is_numeric($quantity)) {
    http_response_code(400);
    echo json_encode(["error" => "Tanto el ID del producto como la cantidad deben ser números."]);
    return;
}

2. Tampoco se comprueba que el productId exista en la base de datos, por lo que habria que comprobar que el producto existe antes de añadirlo al carrito.


*/