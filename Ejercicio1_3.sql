-- Para los productos que tienen varias imagenes, se marcaran todas como false

UPDATE product_images p1
JOIN (
    SELECT product_id
    FROM product_images
    WHERE cover = true
    GROUP BY product_id
    HAVING COUNT(*) > 1
) p2 ON p1.product_id = p2.product_id
SET p1.cover = false;

-- Ahora se seleccionara como verdadera la imagen mas nueva

UPDATE product_images p1
JOIN (
    SELECT MAX(product_image_id) AS max_id, product_id
    FROM product_images
    WHERE cover = false
    GROUP BY product_id
) p2 ON p1.product_id = p2.product_id AND p1.product_image_id = p2.max_id
SET p1.cover = true;
