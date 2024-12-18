<?php
require_once 'Database.php';
class Product extends Database
{

    public function getAllProduct()
    {
        $sql = parent::$connection->prepare('SELECT * FROM products');
        return parent::select($sql);
    }
    public function getNewProductDashboard()
{
    $sql = parent::$connection->prepare('SELECT * FROM products ORDER BY created_at DESC');
    return parent::select($sql);
}

    // Lấy sản phẩm mới nhất
    public function getNewProducts($limit = 10)
    {
        $sql = parent::$connection->prepare('
            SELECT * FROM products 
            ORDER BY created_at DESC 
            LIMIT ?
        ');

        // Binding parameter (MySQLi sử dụng bind_param)
        $sql->bind_param('i', $limit);

        $sql->execute();

        $result = $sql->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    // Hàm tìm kiếm sản phẩm
    public function searchProducts($keyword)
    {
        $sql = parent::$connection->prepare("
            SELECT * FROM products 
            WHERE name LIKE ? OR description LIKE ?
        ");

        $searchTerm = '%' . $keyword . '%';
        $sql->bind_param('ss', $searchTerm, $searchTerm);

        $sql->execute();

        // Kiểm tra lỗi
        if ($sql->error) {
            die("SQL Error: " . $sql->error);
        }

        $result = parent::select($sql);

        // Kiểm tra kết quả
        if (empty($result)) {
            echo "Không tìm thấy sản phẩm với từ khóa: " . $keyword;
        }

        return $result;
    }
    public function getProductByCategory($id)
    {
        // $sql=parent::$connection->prepare('SELECT *
        //                                     FROM products INNER JOIN category_product ON products.id=category_product.product_id
        //                                     WhERE category_product.category_id=?');
        $sql = parent::$connection->prepare('SELECT *
                FROM products 
                WHERE category_id = ?');
        $sql->bind_param('i', $id);
        return parent::select($sql);
    }
    public function getProductById($id)
    {
        $sql = parent::$connection->prepare('SELECT * FROM products WHERE id=?');
        $sql->bind_param('i', $id);
        return parent::select($sql)[0];
    }
    // hàm lấy ra 4 sản phẩm liên quan cho trang chi tiết
    public function getRelatedProducts($categoryId, $productId)
    {

        $sql = parent::$connection->prepare('
        SELECT * FROM products 
        WHERE category_id = ? AND id != ? 
        LIMIT 4
    ');
        $sql->bind_param('ii', $categoryId, $productId);
        return parent::select($sql);
    }
    // Hàm lấy sản phẩm Bestseller Products
    public function getBestsellerProducts($limit = null)
    {
        $sql = parent::$connection->prepare('
             SELECT p.id, p.name, p.description, p.price, p.discount, p.image, 
                    SUM(oi.quantity) AS total_sold
             FROM products p
             INNER JOIN order_items oi ON p.id = oi.product_id
             GROUP BY p.id
             ORDER BY total_sold DESC
             LIMIT ?
         ');

        // Binding parameter
        $sql->bind_param('i', $limit);

        $sql->execute();

        $result = $sql->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function getProductRating($productId)
    {
        // Truy vấn để tính toán rating trung bình
        $sql = parent::$connection->prepare('
             SELECT AVG(rating) AS avg_rating 
             FROM reviews 
             WHERE product_id = ?
         ');

        // Liên kết tham số và thực thi truy vấn
        $sql->bind_param('i', $productId);
        $sql->execute();

        // Lấy kết quả
        $result = $sql->get_result()->fetch_assoc();

        // Trả về rating trung bình, nếu không có rating trả về 0
        return $result['avg_rating'] ?? 0;
    }
    // Lấy 4 sản phẩm đang khuyến mãi
    public function getDiscountedProducts($limit = 4)
    {
        $sql = parent::$connection->prepare('
        SELECT * 
        FROM products 
        WHERE discount > 0 
        ORDER BY discount DESC 
        LIMIT ?
    ');

        // Binding parameter
        $sql->bind_param('i', $limit);

        $sql->execute();

        $result = $sql->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    function paginate($products, $perPage, $currentPage)
    {
        $totalProducts = count($products);
        $totalPages = ceil($totalProducts / $perPage);

        // Đảm bảo trang hiện tại hợp lệ
        $currentPage = max(1, min($currentPage, $totalPages));

        // Tính toán vị trí bắt đầu
        $start = ($currentPage - 1) * $perPage;

        // Cắt mảng sản phẩm theo trang
        $pagedProducts = array_slice($products, $start, $perPage);

        return [
            'data' => $pagedProducts,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'totalProducts' => $totalProducts
        ];
    }
    public function countSearchProducts($keyword)
    {
        // Thêm ký tự "%" cho việc tìm kiếm
        $keyword = "%{$keyword}%";

        // Chuẩn bị câu lệnh SQL
        $sql = parent::$connection->prepare("
            SELECT COUNT(*) as total 
            FROM products 
            WHERE name LIKE ? OR description LIKE ?
        ");

        // Gán giá trị tham số
        $sql->bind_param("ss", $keyword, $keyword);

        // Thực thi câu lệnh
        $sql->execute();

        // Lấy kết quả
        $result = $sql->get_result();
        $row = $result->fetch_assoc();

        // Trả về tổng số sản phẩm
        return $row['total'];
    }
    // Count total products
    public function countAllProducts()
    {
        $stmt = parent::$connection->prepare("SELECT COUNT(*) as total FROM products");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    // Search products with pagination
    public function searchProductsPaginated($keyword, $offset, $limit)
    {
        $keyword = "%{$keyword}%";
        $stmt = parent::$connection->prepare("
        SELECT * 
        FROM products 
        WHERE name LIKE ? OR description LIKE ? 
        LIMIT ? OFFSET ?
    ");
        $stmt->bind_param("ssii", $keyword, $keyword, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get paginated products
    public function getAllProductPaginated($offset, $limit)
    {
        $stmt = parent::$connection->prepare("
        SELECT * 
        FROM products ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function addProduct($name, $description, $price, $discount = 0.00, $category_id, $image, $rating = 0)
    {
        // Chuẩn bị câu lệnh SQL
        $sql = parent::$connection->prepare(
            'INSERT INTO products (name, description, price, discount, category_id, image, rating) 
            VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        // Ràng buộc các tham số với kiểu dữ liệu tương ứng
        $sql->bind_param('ssddisi', $name, $description, $price, $discount, $category_id, $image, $rating);

        // Thực thi câu lệnh
        return $sql->execute();
    }
    public function deleteProduct($id)
    {
        $sql = parent::$connection->prepare('DELETE FROM products WHERE id=?');

        $sql->bind_param('i', $id);
        return $sql->execute();
    }
    public function editProduct($id, $name, $description, $price, $discount = 0, $category_id, $image, $rating = 0)
    {
        $sql = parent::$connection->prepare(
            'UPDATE products 
             SET name = ?, description = ?, price = ?, discount = ?, category_id = ?, image = ?, rating = ? 
             WHERE id = ?'
        );
    
        // Ràng buộc tham số
        $sql->bind_param('ssdiisii', $name, $description, $price, $discount, $category_id, $image, $rating, $id);
    
        // Thực thi câu lệnh
        return $sql->execute();
    }
    
}
