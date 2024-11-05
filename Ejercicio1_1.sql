SELECT product_id, image_id
FROM product_images
GROUP BY product_id, image_id
HAVING COUNT(*) > 1;
