<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสินค้า</title>
    <!-- เพิ่ม Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            margin: 20px;
        }

        h2,
        h3 {
            color: #7F1818;

        }

        h3 {
            margin-top: 20px;
        }

        table {
            margin-bottom: 20px;
        }

        th {
            background-color: #7F1818;
            color: white;
        }

        td,
        th {
            padding: 10px;
            text-align: center;
        }

        .form-control {
            margin-bottom: 10px;
        }

        .btn-primary {
            margin-top: 10px;
        }

        .logout {
            position: absolute;
            top: 10px;
            right: 20px;
            background-color: #7F1818;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
        }

        .logout:hover {
            background-color: #CC0000;
            color: white;
        }

        input[type="submit"] {
            background-color: #B75050;
            /* สีพื้นหลัง (สามารถเปลี่ยนเป็นสีที่ต้องการ) */
            color: white;
            /* สีตัวอักษร */
            padding: 10px 20px;
            /* ขนาด padding */
            border: none;
            /* ไม่มีเส้นขอบ */
            border-radius: 10px;
            /* ทำให้ขอบปุ่มมน */
            cursor: pointer;
            /* เปลี่ยนเคอร์เซอร์เมื่อ hover */
        }

        input[type="submit"]:hover {
            background-color: #FF5858;
            /* สีพื้นหลังเมื่อ hover */
        }

        form {
            background-color: #f2f2f2;
            /* สีพื้นหลังฟอร์ม */
            padding: 20px;
            /* ระยะห่างภายใน */
            border-radius: 10px;
            /* ทำให้ขอบฟอร์มมน */
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            /* เพิ่มเงาให้ฟอร์ม */
            max-width: 500px;
            /* กำหนดความกว้างสูงสุดของฟอร์ม */
            /*margin: auto;*/
            /* จัดฟอร์มให้อยู่ตรงกลาง */
        }

        label,
        input,
        textarea,
        select {
            display: block;
            margin-bottom: 10px;
            /* ระยะห่างระหว่างแต่ละ element */
            width: 100%;
            /* ขยายความกว้างของ input */
            box-sizing: border-box;
            /* ทำให้ padding ของ input รวมอยู่ในความกว้าง */
        }
    </style>
</head>

<body>
    <?php
    session_start();

    // เช็คการเชื่อมต่อฐานข้อมูล
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dbhw10";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
    }

    // เช็คการเข้าสู่ระบบ
    if (!isset($_SESSION['username']) || !$_SESSION['is_manager']) {
        header("Location: index.html");
        exit();
    }
    ?>
    <header>
        <div class="d-flex justify-content-between align-items-center">
            <h2>Manage products</h2>

            <a href="index.html" class="logout">ออกจากระบบ</a>

        </div>
    </header>

    <?php
    //ตรวจสอบค่าซ้ำ
    function isDuplicate($conn, $table, $column, $value)
    {
        $query = "SELECT * FROM $table WHERE $column = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0; // คืนค่า true ถ้ามีค่าซ้ำ
    }

    // ฟังก์ชันเพิ่มสินค้า ถ้าสินค้าไม่มีในระบบ
    function addProductOnce($conn, $name, $price, $stock_quantity, $category, $image_path = null, $details)
    {
        $check_query = "SELECT * FROM products WHERE name = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $check_result = $stmt->get_result();

        if ($check_result->num_rows > 0) {
            return;
        }

        // สร้างคำสั่ง SQL สำหรับการเพิ่มสินค้า
        $insert_query = "INSERT INTO products (name, price, stock_quantity, category, image, details) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sissss", $name, $price, $stock_quantity, $category, $image_path, $details);

        if ($stmt->execute()) {
            echo "<script>alert('เพิ่มสินค้าสำเร็จ!'); window.location.href='manager.php';</script>";
        } else {
            echo "เกิดข้อผิดพลาด: " . $conn->error;
        }
    }
    // เพิ่มสินค้า
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
        $new_product_name = $_POST['new_product_name'];
        $new_product_price = $_POST['new_product_price'];
        $new_product_category = $_POST['new_product_category'];
        $new_product_stock = $_POST['new_product_stock'];
        $details = $_POST['product_details']; // รายละเอียดสินค้า

        // ตรวจสอบค่าซ้ำก่อนเพิ่มสินค้า
        if (isDuplicate($conn, 'products', 'name', $new_product_name)) {
            echo "<script>alert('ชื่อสินค้านี้มีอยู่ในระบบแล้ว กรุณาเลือกชื่อใหม่.');</script>";
        } else {
            // ตรวจสอบการอัปโหลดรูปภาพ
            $image_path = null; // กำหนดค่าเริ่มต้นสำหรับรูปภาพ
            if (isset($_FILES['new_product_image']) && $_FILES['new_product_image']['error'] == UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/'; // กำหนดโฟลเดอร์ที่เก็บรูปภาพ
                $uploaded_file = $_FILES['new_product_image'];
                $image_path = $upload_dir . basename($uploaded_file['name']); // สร้างที่อยู่สำหรับรูปภาพใหม่

                // อัปโหลดไฟล์ไปยังโฟลเดอร์ที่กำหนด
                if (move_uploaded_file($uploaded_file['tmp_name'], $image_path)) {
                } else {
                    $image_path = null; // ถ้าเกิดข้อผิดพลาดให้กลับไปใช้ค่า null
                }
            }

            // เพิ่มสินค้าลงในฐานข้อมูล
            addProductOnce($conn, $new_product_name, $new_product_price, $new_product_stock, $new_product_category, $image_path, $details);
        }
    }

    // เพิ่มสินค้า
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
        $new_product_name = $_POST['new_product_name'];
        $new_product_price = $_POST['new_product_price'];
        $new_product_category = $_POST['new_product_category'];
        $new_product_stock = $_POST['new_product_stock'];
        $details = $_POST['product_details']; // รายละเอียดสินค้า

        // ตรวจสอบการอัปโหลดรูปภาพ
        $image_path = null; // กำหนดค่าเริ่มต้นสำหรับรูปภาพ
        if (isset($_FILES['new_product_image']) && $_FILES['new_product_image']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/'; // กำหนดโฟลเดอร์ที่เก็บรูปภาพ
            $uploaded_file = $_FILES['new_product_image'];
            $image_path = $upload_dir . basename($uploaded_file['name']); // สร้างที่อยู่สำหรับรูปภาพใหม่

            // อัปโหลดไฟล์ไปยังโฟลเดอร์ที่กำหนด
            if (move_uploaded_file($uploaded_file['tmp_name'], $image_path)) {
            } else {
                $image_path = null; // ถ้าเกิดข้อผิดพลาดให้กลับไปใช้ค่า null
            }
        }

        // เพิ่มสินค้าลงในฐานข้อมูล
        addProductOnce($conn, $new_product_name, $new_product_price, $new_product_stock, $new_product_category, $details, $image_path);
    }

    // แสดงหมวดหมู่ทั้งหมด
    function displayCategories($conn)
    {
        echo "<h2>หมวดหมู่สินค้า</h2>";
        echo "<table border='1'>
            <tr>
                <th>ลำดับที่</th>
                <th>ID</th>
                <th>ชื่อหมวดหมู่</th>
                <th>ดำเนินการ</th>  
            </tr>";

        $result = $conn->query("SELECT * FROM categories");
        if (!$result) {
            echo "An error occurred retrieving category information: " . $conn->error;
            return;
        }

        $counter = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>" . $counter++ . "</td>
                <td>" . $row["id"] . "</td>
                <td>" . $row["name"] . "</td>
                <td>
                    <a href='?edit_category_id=" . $row["id"] . "'>แก้ไข</a>
                    <a href='?delete_category_id=" . $row["id"] . "' onclick='return confirm(\"ยืนยันการลบ?\");'>ลบ</a>
                </td>
              </tr>";
        }
        echo "</table>";
    }

    // ลบหมวดหมู่
    if (isset($_GET['delete_category_id'])) {
        $delete_category_id = $_GET['delete_category_id'];
        $conn->query("DELETE FROM categories WHERE id=$delete_category_id");
        header("Location: manager.php");
        exit();
    }

    // แก้ไขหมวดหมู่
    $category = null;
    if (isset($_GET['edit_category_id'])) {
        $edit_category_id = $_GET['edit_category_id'];
        $result = $conn->query("SELECT * FROM categories WHERE id=$edit_category_id");
        $category = $result->fetch_assoc();
    }

    // ฟอร์มเพิ่มหมวดหมู่
    ?>

    <?php

    // เพิ่มหมวดหมู่
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
        $new_category_name = $_POST['new_category_name'];

        // ตรวจสอบค่าซ้ำก่อนเพิ่มหมวดหมู่
        if (isDuplicate($conn, 'categories', 'name', $new_category_name)) {
            echo "<script>alert('ชื่อหมวดหมู่นี้มีอยู่ในระบบแล้ว กรุณาเลือกชื่อใหม่.');</script>";
        } else {
            // สร้างคำสั่ง SQL สำหรับการเพิ่มหมวดหมู่
            $insert_query = "INSERT INTO categories (name) VALUES (?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("s", $new_category_name);

            if ($stmt->execute()) {
                // ใช้ JavaScript Alert สำหรับการแจ้งเตือนสำเร็จ
                echo "<script>alert('เพิ่มหมวดหมู่สำเร็จ!');</script>";
            } else {
                // ใช้ JavaScript Alert สำหรับการแจ้งเตือนข้อผิดพลาด
                echo "<script>alert('เกิดข้อผิดพลาด: " . addslashes($conn->error) . "');</script>";
            }
        }
    }



    // ฟอร์มแก้ไขหมวดหมู่ (แสดงเมื่อมีการเลือกแก้ไขหมวดหมู่)
    if ($category) {
    ?>
        <h3>แก้ไขหมวดหมู่</h3>
        <form action="" method="POST">
            <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
            <label for="edit_category_name">ชื่อหมวดหมู่:</label>
            <input type="text" id="edit_category_name" name="edit_category_name" value="<?php echo $category['name']; ?>" required>
            <br>
            <input type="submit" name="update_category" value="อัปเดตหมวดหมู่">
        </form>
    <?php
    }

    // อัปเดตหมวดหมู่
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) {
        $category_id = $_POST['category_id'];
        $updated_category_name = $_POST['edit_category_name'];

        $update_query = "UPDATE categories SET name = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $updated_category_name, $category_id);

        if ($stmt->execute()) {
            echo "อัปเดตหมวดหมู่สำเร็จ!";
        } else {
            echo "เกิดข้อผิดพลาด: " . $conn->error;
        }
    }

    // แสดงข้อมูลสินค้า
    echo "<h2>ข้อมูลสินค้า</h2>";
    echo "<table border='1'>
        <tr>
            <th>ลำดับที่</th>
            <th>ID</th>
            <th>ชื่อสินค้า</th>
            <th>ราคา (THB)</th>
            <th>จำนวนสินค้าในสต็อก</th>
            <th>หมวดหมู่</th>
            <th>รายละเอียดสินค้า</th> <!-- New Column -->
            <th>ดำเนินการ</th>  
        </tr>";

    $result = $conn->query("SELECT * FROM products");
    $counter = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>" . $counter++ . "</td>
            <td>" . $row["id"] . "</td>
            <td>" . $row["name"] . "</td>
            <td>" . $row["price"] . "</td>
            <td>" . $row["stock_quantity"] . "</td>
            <td>" . $row["category"] . "</td>
            <td>" . $row["details"] . "</td> <!-- Display detail -->
            <td>
                <a href='?edit_id=" . $row["id"] . "'>แก้ไข</a>
                <a href='?delete_id=" . $row["id"] . "' onclick='return confirm(\"ยืนยันการลบ?\");'>ลบ</a>
            </td>
          </tr>";
    }
    echo "</table>";

    // แสดงหมวดหมู่ทั้งหมด   
    displayCategories($conn);

    // ลบข้อมูลสินค้า
    if (isset($_GET['delete_id'])) {
        $delete_id = $_GET['delete_id'];

        // ลบสินค้า
        $conn->query("DELETE FROM products WHERE id=$delete_id");

        // ตรวจสอบว่าการลบสำเร็จหรือไม่
        if ($conn->affected_rows > 0) {
            echo "<script>alert('ลบสินค้าสำเร็จ!'); window.location.href='manager.php';</script>";
        } else {
            echo "<script>alert('ไม่พบสินค้าที่ต้องการลบ.'); window.location.href='manager.php';</script>";
        }
        exit();
    }

    // แก้ไขสินค้า
    $product = null; // Initialize the product variable
    if (isset($_GET['edit_id'])) {
        $edit_product_id = $_GET['edit_id'];
        $result = $conn->query("SELECT * FROM products WHERE id=$edit_product_id");
        $product = $result->fetch_assoc();
    }

    ?>


    <h3>เพิ่มสินค้า</h3>
    <div class="form-container">
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="new_product_name">ชื่อสินค้า:</label>
            <input type="text" id="new_product_name" name="new_product_name" required>
            <br>

            <label for="new_product_price">ราคา (THB):</label>
            <input type="number" id="new_product_price" name="new_product_price" required>
            <br>

            <label for="new_product_stock">จำนวนสินค้าในสต็อก:</label>
            <input type="number" id="new_product_stock" name="new_product_stock" required>
            <br>

            <label for="new_product_details">รายละเอียดสินค้า:</label>
            <textarea id="new_product_details" name="product_details" required></textarea>
            <br>

            <label for="new_product_category">หมวดหมู่:</label>
            <select id="new_product_category" name="new_product_category" required>
                <?php
                $category_result = $conn->query("SELECT * FROM categories");
                while ($category_row = $category_result->fetch_assoc()) {
                    echo "<option value='" . $category_row['name'] . "'>" . $category_row['name'] . "</option>";
                }
                ?>
            </select>
            <br>

            <label for="new_product_image">อัปโหลดรูปภาพ:</label>
            <input type="file" id="new_product_image" name="new_product_image" accept="image/*" required>
            <br>

            <input type="submit" name="add_product" value="เพิ่มสินค้า">
        </form>

        <h3>เพิ่มหมวดหมู่ใหม่</h3>
        <form action="" method="POST">
            <label for="new_category_name">ชื่อหมวดหมู่:</label>
            <input type="text" id="new_category_name" name="new_category_name" required>
            <br>
            <input type="submit" name="add_category" value="เพิ่มหมวดหมู่">
        </form>
    </div>
    <?php
    // ฟอร์มแก้ไขสินค้า (ถ้าเลือกสินค้าเพื่อแก้ไข)
    if ($product) {
    ?>
        <h3>แก้ไขสินค้า</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

            <label for="edit_product_name">ชื่อสินค้า:</label>
            <input type="text" id="edit_product_name" name="edit_product_name" value="<?php echo $product['name']; ?>" required>
            <br>

            <label for="edit_product_price">ราคา (THB):</label>
            <input type="number" id="edit_product_price" name="edit_product_price" value="<?php echo $product['price']; ?>" required>
            <br>

            <label for="edit_product_stock">จำนวนสินค้าในสต็อก:</label>
            <input type="number" id="edit_product_stock" name="edit_product_stock" value="<?php echo $product['stock_quantity']; ?>" required>
            <br>

            <label for="edit_product_details">รายละเอียดสินค้า:</label>
            <textarea id="edit_product_details" name="edit_product_details" required><?php echo $product['details']; ?></textarea>
            <br>

            <label for="edit_product_category">หมวดหมู่:</label>
            <select id="edit_product_category" name="edit_product_category" required>
                <?php
                $category_result = $conn->query("SELECT * FROM categories");
                while ($category_row = $category_result->fetch_assoc()) {
                    $selected = ($category_row['name'] == $product['category']) ? "selected" : "";
                    echo "<option value='" . $category_row['name'] . "' $selected>" . $category_row['name'] . "</option>";
                }
                ?>
            </select>
            <br>

            <label for="edit_product_image">อัปโหลดรูปภาพใหม่ (ไม่บังคับ):</label>
            <input type="file" id="edit_product_image" name="edit_product_image" accept="image/*">
            <br>

            <?php if (!empty($product['image'])): ?>
                <p>รูปภาพปัจจุบัน:</p>
                <img src="<?php echo $product['image']; ?>" alt="Current Product Image" style="width: 100px; height: auto;">
            <?php endif; ?>

            <input type="submit" name="update_product" value="อัปเดตสินค้า">
        </form>
    <?php
    }

    // อัปเดตสินค้า
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
        $product_id = $_POST['product_id'];
        $updated_name = $_POST['edit_product_name'];
        $updated_price = $_POST['edit_product_price'];
        $updated_stock = $_POST['edit_product_stock'];
        $updated_category = $_POST['edit_product_category'];
        $updated_details = $_POST['edit_product_details'];

        // อัปโหลดรูปภาพใหม่ถ้ามีการเลือกไฟล์ใหม่
        if (isset($_FILES['edit_product_image']) && $_FILES['edit_product_image']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            $uploaded_file = $_FILES['edit_product_image'];
            $image_path = $upload_dir . basename($uploaded_file['name']);

            if (move_uploaded_file($uploaded_file['tmp_name'], $image_path)) {
                echo "อัปโหลดรูปภาพใหม่สำเร็จ!";
            }
        } else {
            $image_path = $product['image']; // ถ้าไม่มีการอัปโหลดใหม่ให้ใช้รูปภาพเดิม
        }

        // อัปเดตสินค้าลงฐานข้อมูล
        $update_query = "UPDATE products SET name = ?, price = ?, stock_quantity = ?, category = ?, image = ?, details = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sissssi", $updated_name, $updated_price, $updated_stock, $updated_category, $image_path, $updated_details, $product_id);

        if ($stmt->execute()) {
            echo "<script>alert('อัปเดตสินค้าสำเร็จ!'); window.location.href='manager.php';</script>";
        } else {
            echo "เกิดข้อผิดพลาด: " . $conn->error;
        }
    }


    // ปิดการเชื่อมต่อฐานข้อมูล
    $conn->close();
    ?>