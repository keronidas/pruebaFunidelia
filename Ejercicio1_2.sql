WITH imágenes AS (
    SELECT 
        product_image_id,
        product_id,
        image_id,
        ROW_NUMBER() OVER (PARTITION BY product_id, image_id ORDER BY product_image_id) AS numero_fila
    FROM 
        product_images
)

DELETE FROM product_images
WHERE product_image_id IN (
    SELECT product_image_id FROM imágenes WHERE numero_fila > 1
);
