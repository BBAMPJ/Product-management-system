<?php
session_start(); // เริ่มเซสชัน

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'dbhw10'); // แก้ไขข้อมูลตามจริง
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีการสร้างเซสชันสำหรับตะกร้าหรือยัง
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // สร้างอาเรย์ว่างสำหรับตะกร้า
}

// ตรวจสอบการลบสินค้าจากตะกร้า
if (isset($_POST['remove_from_cart'])) {
    $product_id = $_POST['product_id']; // ID ของสินค้า
    if (($key = array_search($product_id, $_SESSION['cart'])) !== false) {
        unset($_SESSION['cart'][$key]); // ลบสินค้าจากตะกร้า
    }
}

// อัปเดตจำนวนสินค้าในตะกร้า
$cart_item_count = count($_SESSION['cart']); // นับจำนวนสินค้าที่มีอยู่ในตะกร้า

// ดึงข้อมูลสินค้าจากตะกร้า
$cart_items = $_SESSION['cart'];

// ดึงข้อมูลสินค้าจากฐานข้อมูลตาม ID
if (!empty($cart_items)) {
    $product_ids = implode(',', array_map('intval', $cart_items));
    $sql = "SELECT * FROM products WHERE id IN ($product_ids)";
    $result = $conn->query($sql);
} else {
    $result = null; // ไม่มีสินค้าในตะกร้า
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #E6F2FF;
            color: #333;
            padding: 20px;
            position: relative;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 15px;
            text-align: center;
        }

        th {
            background-color: #7F1818;
            color: white;
        }

        .remove-btn {
            background-color: #D9534F;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .remove-btn:hover {
            background-color: #C9302C;
        }
    </style>
</head>

<body>
    <a href="main.php" style="position: absolute; top: 20px; right: 20px; padding: 10px 15px; background-color: #007BFF; color: white; border: none; border-radius: 5px; text-decoration: none;">กลับ</a>

    <h1>ตะกร้าสินค้า</h1>

    <?php if (empty($cart_items)) : ?>
        <p>ตะกร้าสินค้าของคุณว่างเปล่า</p>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>ชื่อสินค้า</th>
                    <th>ราคา</th>
                    <th>จำนวน</th>
                    <th>ดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['price']); ?> บาท</td>
                        <td>1</td> <!-- แสดงจำนวนที่เพิ่มไปยังตะกร้า -->
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                                <button type="submit" name="remove_from_cart" class="remove-btn">ลบ</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <footer>
        <p>&copy; 2024 ระบบจัดการสต็อกสินค้า</p>
    </footer>
</body>

</html>