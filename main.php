<?php
session_start(); // เริ่มเซสชัน

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'dbhw10'); // แก้ไขข้อมูลตามจริง
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตัวแปรสำหรับการค้นหาสินค้า
$search_keyword = '';
$category = '';

// ตรวจสอบการค้นหาสินค้า
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['search_keyword'])) {
        $search_keyword = $_POST['search_keyword'];
    }
    if (isset($_POST['category'])) {
        $category = $_POST['category'];
    }
    // ตรวจสอบการเพิ่มสินค้าไปยังตระกร้า
    if (isset($_POST['add_to_cart'])) {
        $product_id = $_POST['product_id']; // ID ของสินค้า
        $_SESSION['cart'][] = $product_id; // เพิ่มสินค้าไปยังตระกร้า
    }
}

// ดึงข้อมูลหมวดหมู่
$category_sql = "SELECT * FROM categories";
$category_result = $conn->query($category_sql);

// ดึงข้อมูลสินค้า
$sql = "SELECT * FROM products WHERE name LIKE ? AND category LIKE ?";
$stmt = $conn->prepare($sql);
$keyword_param = '%' . $search_keyword . '%';
$category_param = '%' . $category . '%';
$stmt->bind_param('ss', $keyword_param, $category_param);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>wristwatch</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #E6F2FF;
            color: #333;
            transition: background-color 0.5s ease;
        }

        header {
            background-color: #7F1818;
            color: white;
            padding: 20px 0;
            text-align: center;
            position: relative;
            /* เพิ่มตำแหน่งสัมพัทธ์ */
            animation: fadeIn 1s;
            /* เพิ่มการเคลื่อนไหว */
        }

        .user-info {
            position: absolute;
            /* ทำให้สามารถจัดตำแหน่งได้ */
            right: 20px;
            /* ห่างจากขอบขวา */
            top: 20px;
            /* ห่างจากขอบบน */
            font-weight: bold;
            /* ขีดตัวหนา */

        }

        nav ul {
            list-style-type: none;
            /* ไม่มีจุดหน้ารายการ */
            padding: 0;
        }

        nav ul li {
            display: inline;
            /* แสดงรายการในแถวเดียว */
            margin: 0 15px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            /* ไม่มีเส้นใต้ */
            font-weight: bold;
            transition: color 0.3s;
            /* การเปลี่ยนสีของลิงก์ */
        }

        nav ul li a:hover {
            color: #FFD700;
            /* สีทองเมื่อเลื่อนเมาส์ */
        }

        main {
            padding: 20px;
        }

        section {
            margin-bottom: 30px;
            background-color: white;
            /* พื้นหลังของแต่ละส่วน */
            padding: 20px;
            border-radius: 5px;
            /* มุมมน */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            /* เงา */
            animation: slideIn 0.5s;
            /* เพิ่มการเคลื่อนไหว */
        }

        .product-list {
            display: flex;
            /* ใช้ Flexbox เพื่อจัดเรียงสินค้า */
            flex-wrap: wrap;
            /* ให้สินค้าอยู่ในบรรทัดใหม่ได้ */
            gap: 10px;
            /* ระยะห่างระหว่างสินค้า */
        }

        .product-item {
            background-color: #f9f9f9;
            /* พื้นหลังของสินค้า */
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            /* เงา */
            text-align: center;
            /* จัดตำแหน่งข้อความให้อยู่กลาง */
            flex: 1 1 calc(30% - 20px);
            /* กำหนดขนาดสินค้า */
            transition: transform 0.3s;
            /* การเคลื่อนไหว */
        }

        .product-item:hover {
            transform: scale(1.05);
            /* ขยายขนาดสินค้าเมื่อเลื่อนเมาส์ */
        }

        .product-item img {
            max-width: 150px;
            /* รูปสินค้าไม่เกินขนาด */
            height: 200px;
            /* กำหนดความสูง */
            object-fit: cover;
            /* ปรับภาพให้พอดีกับขนาด */
        }

        .product-item button {
            background-color: #9A0000;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            margin-left: 10px;
            cursor: pointer;
            /* เปลี่ยนเคอร์เซอร์เมื่อเลื่อนเมาส์ */
            transition: background-color 0.3s;
            /* การเปลี่ยนสีของปุ่ม */
        }

        .product-item button:hover {
            background-color: #0056b3;
            /* สีน้ำเงินเข้มเมื่อเลื่อนเมาส์ */
        }

        footer {
            text-align: center;
            padding: 10px 0;
            background-color: #7F1818;
            /* สีฟ้า */
            color: white;
        }

        .popup {
            display: none;
            /* ซ่อนป๊อปอัพ */
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            /* อยู่ด้านบน */
        }

        .popup-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            width: 80%;
            max-width: 500px;
            max-height: 60%;
            overflow-y: auto;
            text-align: center;
        }

        .popup-content img {
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 400px;
            object-fit: cover;
        }


        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Animation Keyframes */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        a.btn.btn-danger {
            color: #FFD700;
            /* เปลี่ยนสีข้อความเป็นสีทอง */
        }

        a.btn.btn-danger:hover {
            color: #FFFF29;
            /* เปลี่ยนสีเมื่อวางเมาส์ */
        }

        #products form {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
            margin-bottom: 20px;
        }

        #products input[type="text"] {
            padding: 12px;
            font-size: 16px;
            width: 300px;
            /* ขยายความกว้างของช่องค้นหา */
            border: 2px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s ease;
        }

        #products input[type="text"]:focus {
            border-color: #0056b3;
            /* เพิ่มสีเมื่อเลือกช่องค้นหา */
        }

        #products select {
            padding: 12px;
            font-size: 16px;
            border: 2px solid #ccc;
            border-radius: 5px;
        }

        #products button {
            background-color: #9A0000;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #products button:hover {
            background-color: #FFD700;
            color: black;
            /* เปลี่ยนสีปุ่มเมื่อเลื่อนเมาส์ */
        }
    </style>
    <script>
        function openPopup(name, details, price, stock, image) {
            document.getElementById("popup-name").innerText = name;
            document.getElementById("popup-details").innerText = details;
            document.getElementById("popup-price").innerText = "ราคา: " + price + " บาท";
            document.getElementById("popup-stock").innerText = "จำนวนในสต็อก: " + stock;
            document.getElementById("popup-image").src = image;
            document.getElementById("popup").style.display = "block";
        }

        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }
    </script>
</head>

<body>
    <header>
        <div class="d-flex justify-content-between align-items-center">
            <h2>WRISTWTCH</h2>
            <div class="user-info">
                <span>Hi, <?php echo htmlspecialchars(isset($_SESSION['username']) ? $_SESSION['username'] : 'ผู้ใช้'); ?></span>
                <a href="index.html" class="btn btn-danger btn-sm ml-2"> Log out</a>
            </div>
            <nav>
                <ul>
                    <li><a href="#about">About</a></li>
                    <li><a href="#products">Product</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="index.html">Home page</a></li>
                    <li><a href="cart.php">ตะกร้าสินค้า (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a></li>

                </ul>
            </nav>
        </div>
    </header>
    <main>
        <section id="about">
            <h2>about</h2>
            <p>Luxurious wristwatch Suitable for fashionistas, business people, athletes, and those looking for a watch that meets their lifestyle needs.</p>
        </section>

        <section id="products">
            <h2>Product</h2>
            <form method="POST">
                <input type="text" name="search_keyword" placeholder="ค้นหาสินค้า..." value="<?php echo htmlspecialchars($search_keyword); ?>">
                <select name="category">
                    <option value="">Choose a category</option>
                    <?php
                    // แสดงหมวดหมู่จากฐานข้อมูล
                    while ($row = $category_result->fetch_assoc()) {
                        $selected = ($category === $row['name']) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($row['name']) . '" ' . $selected . '>' . htmlspecialchars($row['name']) . '</option>';
                    }
                    ?>
                </select>
                <button type="submit">search</button>
            </form>
            <div class="product-list">
                <?php
                // แสดงรายการสินค้า
                while ($row = $result->fetch_assoc()) {
                    echo '<article class="product-item">';
                    echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                    echo '<img src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                    echo '<p>ราคา: ' . htmlspecialchars($row['price']) . ' บาท</p>';
                    echo '<p>หมวดหมู่: ' . htmlspecialchars($row['category']) . '</p>';
                    echo '<button onclick="openPopup(\'' . addslashes($row['name']) . '\', \'' . addslashes($row['details']) . '\', ' . htmlspecialchars($row['price']) . ', ' . htmlspecialchars($row['stock_quantity']) . ', \'' . addslashes($row['image']) . '\')">ดูรายละเอียด</button>';
                    echo '<form method="POST" style="display:inline;">';
                    echo '<input type="hidden" name="product_id" value="' . htmlspecialchars($row['id']) . '">'; // เพิ่ม ID ของสินค้า
                    echo '<button type="submit" name="add_to_cart">เพิ่มไปยังตระกร้า</button>'; // ปุ่มเพิ่มไปยังตระกร้า
                    echo '</form>';
                    echo '</article>';
                }
                ?>
            </div>
        </section>

        <section id="contact">
            <h2>Contact</h2>
            <p>You can contact us via email at support@example.com</p>
        </section>
    </main>

    <footer>
        <p>&copy; Trending Wristwatches</p>
    </footer>

    <!-- ป๊อปอัพ -->
    <div id="popup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <h2 id="popup-name"></h2>
            <img id="popup-image" src="" alt="Product Image">
            <p id="popup-details"></p>
            <p id="popup-price"></p>
            <p id="popup-stock"></p>
        </div>
    </div>
</body>

</html>