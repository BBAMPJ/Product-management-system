<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผู้ใช้</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h2,
        h3 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #7F1818;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #7F1818;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        input[type="submit"]:hover {
            background-color: #CC0000;
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
        }

        .autocomplete-suggestions {
            position: absolute;
            background-color: white;
            border: 1px solid white;
            max-height: 200px;
            overflow-y: auto;
            width: 30%;
            z-index: 99;
        }

        .autocomplete-suggestion {
            padding: 10px;
            cursor: pointer;
        }

        .autocomplete-suggestion:hover {
            background-color: #f0f0f0;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <a href="index.html" class="logout">ออกจากระบบ</a>

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
    if (!isset($_SESSION['username']) || !$_SESSION['isAdmin']) {
        header("Location: index.html");
        exit();
    }

    // ฟังก์ชันแสดงข้อมูลผู้ใช้
    echo "<h2>ข้อมูลผู้ใช้</h2>";
    echo "<table border='1'>
        <tr>
            <th>ลำดับที่</th>
            <th>ID</th>
            <th>ชื่อผู้ใช้</th>
            <th>ชื่อ</th>
            <th>นามสกุล</th>
            <th>เพศ</th>
            <th>อายุ</th>
            <th>จังหวัด</th>
            <th>อีเมล์</th>
            <th>สถานะ</th>
            <th>การจัดการ</th>
        </tr>";

    $result = $conn->query("SELECT * FROM users");
    $counter = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>" . $counter++ . "</td>
            <td>" . $row["id"] . "</td>
            <td>" . $row["username"] . "</td>
            <td>" . $row["firstname"] . "</td>
            <td>" . $row["lastname"] . "</td>
            <td>" . $row["gender"] . "</td>
            <td>" . $row["age"] . "</td>
            <td>" . $row["province"] . "</td>
            <td>" . $row["email"] . "</td>
            <td>" . ($row["is_manager"] ? 'ผู้จัดการ' : 'ลูกค้าทั่วไป') . "</td>
            <td>
                <a href='?edit_id=" . $row["id"] . "'>แก้ไข</a>
                <a href='?delete_id=" . $row["id"] . "' onclick='return confirm(\"ยืนยันการลบ?\");'>ลบ</a>
            </td>
          </tr>";
    }
    echo "</table>";

    // ลบข้อมูลผู้ใช้
    if (isset($_GET['delete_id'])) {
        $delete_id = $_GET['delete_id'];
        $conn->query("DELETE FROM users WHERE id=$delete_id");
        header("Location: admin.php");
        exit();
    }

    // แสดงฟอร์มแก้ไขข้อมูลผู้ใช้
    if (isset($_GET['edit_id'])) {
        $edit_id = $_GET['edit_id'];
        $edit_result = $conn->query("SELECT * FROM users WHERE id=$edit_id");
        $edit_row = $edit_result->fetch_assoc();
    ?>
        <h3>แก้ไขข้อมูลผู้ใช้</h3>
        <form action="" method="POST">
            <input type="hidden" name="edit_id" value="<?php echo $edit_row['id']; ?>">
            <label for="edit_username">ชื่อผู้ใช้:</label>
            <input type="text" id="edit_username" name="edit_username" value="<?php echo $edit_row['username']; ?>" required>
            <br>
            <label for="edit_firstname">ชื่อ:</label>
            <input type="text" id="edit_firstname" name="edit_firstname" value="<?php echo $edit_row['firstname']; ?>" required>
            <br>
            <label for="edit_lastname">นามสกุล:</label>
            <input type="text" id="edit_lastname" name="edit_lastname" value="<?php echo $edit_row['lastname']; ?>" required>
            <br>
            <label for="edit_gender">เพศ:</label>
            <select id="edit_gender" name="edit_gender" required>
                <option value="male" <?php if ($edit_row['gender'] == 'male') echo 'selected'; ?>>ชาย</option>
                <option value="female" <?php if ($edit_row['gender'] == 'female') echo 'selected'; ?>>หญิง</option>
                <option value="other" <?php if ($edit_row['gender'] == 'other') echo 'selected'; ?>>อื่นๆ</option>
            </select>
            <br>
            <label for="edit_age">อายุ:</label>
            <input type="number" id="edit_age" name="edit_age" value="<?php echo $edit_row['age']; ?>" required>
            <br>
            <label for="edit_province">จังหวัด:</label>
            <select id="edit_province" name="edit_province" required>
                <option value="">เลือกจังหวัด</option>
                <?php
                $provinces = [
                    "กรุงเทพมหานคร",
                    "กระบี่",
                    "กาญจนบุรี",
                    "กาฬสินธุ์",
                    "กำแพงเพชร",
                    "ขอนแก่น",
                    "จันทบุรี",
                    "ชลบุรี",
                    "ชัยนาท",
                    "ชัยภูมิ",
                    "นครปฐม",
                    "นครราชสีมา",
                    "นนทบุรี",
                    "ปทุมธานี",
                    "พระนครศรีอยุธยา",
                    "ภูเก็ต",
                    "เชียงใหม่",
                    "เชียงราย"
                ];
                foreach ($provinces as $province) {
                    $selected = ($province == $edit_row['province']) ? 'selected' : '';
                    echo "<option value='$province' $selected>$province</option>";
                }
                ?>
            </select>
            <br>
            <label for="edit_email">อีเมล์:</label>
            <input type="email" id="edit_email" name="edit_email" value="<?php echo $edit_row['email']; ?>" required>
            <br>
            <label for="edit_is_manager">สิทธิ์:</label>
            <select id="edit_is_manager" name="edit_is_manager">
                <option value="0" <?php if ($edit_row['is_manager'] == 0) echo 'selected'; ?>>ลูกค้าทั่วไป</option>
                <option value="1" <?php if ($edit_row['is_manager'] == 1) echo 'selected'; ?>>ผู้จัดการ</option>
            </select>
            <br>
            <input type="submit" value="บันทึกการเปลี่ยนแปลง">
        </form>
    <?php
    }


    // อัปเดตข้อมูลผู้ใช้ในฐานข้อมูล
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'])) {
        $edit_id = $_POST['edit_id'];
        $edit_username = $_POST['edit_username'];
        $edit_firstname = $_POST['edit_firstname'];
        $edit_lastname = $_POST['edit_lastname'];
        $edit_gender = $_POST['edit_gender'];
        $edit_age = $_POST['edit_age'];
        $edit_province = $_POST['edit_province'];
        $edit_email = $_POST['edit_email'];
        $edit_is_manager = $_POST['edit_is_manager'];

        $update_sql = "UPDATE users SET username='$edit_username', firstname='$edit_firstname', lastname='$edit_lastname', 
                   gender='$edit_gender', age=$edit_age, province='$edit_province', email='$edit_email', is_manager=$edit_is_manager 
                   WHERE id=$edit_id";
        if ($conn->query($update_sql) === TRUE) {
            echo "อัปเดตข้อมูลผู้ใช้สำเร็จ!";
            header("Location: admin.php");
            exit();
        } else {
            echo "เกิดข้อผิดพลาด: " . $conn->error;
        }
    }

    // ฟอร์มเพิ่มบัญชีผู้ใช้ใหม่
    ?>
    <h3>เพิ่มบัญชีผู้ใช้</h3>
    <form action="" method="POST">
        <label for="new_username">ชื่อผู้ใช้:</label>
        <input type="text" id="new_username" name="new_username" required>
        <br>
        <label for="new_password">รหัสผ่าน:</label>
        <input type="password" id="new_password" name="new_password" required>
        <br>
        <label for="new_confirm_password">ยืนยันรหัสผ่าน:</label>
        <input type="password" id="new_confirm_password" name="new_confirm_password" required>
        <br>
        <label for="new_firstname">ชื่อ:</label>
        <input type="text" id="new_firstname" name="new_firstname" required>
        <br>
        <label for="new_lastname">นามสกุล:</label>
        <input type="text" id="new_lastname" name="new_lastname" required>
        <br>
        <label for="new_gender">เพศ:</label>
        <select id="new_gender" name="new_gender" required>
            <option value="male">ชาย</option>
            <option value="female">หญิง</option>
            <option value="other">อื่นๆ</option>
        </select>
        <br>
        <label for="new_age">อายุ:</label>
        <input type="number" id="new_age" name="new_age" required>
        <br>
        <label for="new_province">จังหวัด:</label>
        <input type="text" id="province" name="province" required autocomplete="off">
        <div id="autocomplete-list" class="autocomplete-suggestions"></div>
        <script>
            const provinces = [
                "กระบี่", "กรุงเทพมหานคร", "กาญจนบุรี", "กาฬสินธุ์", "กำแพงเพชร",
                "ขอนแก่น", "จันทบุรี", "ฉะเชิงเทรา", "ชลบุรี", "ชัยนาท",
                "ชัยภูมิ", "ชุมพร", "เชียงราย", "เชียงใหม่", "ตรัง",
                "ตราด", "ตาก", "นครนายก", "นครปฐม", "นครพนม",
                "นครราชสีมา", "นครศรีธรรมราช", "นครสวรรค์", "นนทบุรี",
                "นราธิวาส", "น่าน", "บึงกาฬ", "บุรีรัมย์", "ปทุมธานี",
                "ประจวบคีรีขันธ์", "ปราจีนบุรี", "ปัตตานี", "พระนครศรีอยุธยา",
                "พะเยา", "พังงา", "พัทลุง", "พิจิตร", "พิษณุโลก",
                "เพชรบุรี", "เพชรบูรณ์", "แพร่", "ภูเก็ต", "มหาสารคาม",
                "มุกดาหาร", "แม่ฮ่องสอน", "ยโสธร", "ยะลา", "ร้อยเอ็ด",
                "ระนอง", "ระยอง", "ราชบุรี", "ลพบุรี", "ลำปาง",
                "ลำพูน", "เลย", "ศรีสะเกษ", "สกลนคร", "สงขลา",
                "สตูล", "สมุทรปราการ", "สมุทรสงคราม", "สมุทรสาคร",
                "สระแก้ว", "สระบุรี", "สิงห์บุรี", "สุโขทัย",
                "สุพรรณบุรี", "สุราษฎร์ธานี", "สุรินทร์", "หนองคาย",
                "หนองบัวลำภู", "อ่างทอง", "อำนาจเจริญ", "อุดรธานี",
                "อุตรดิตถ์", "อุทัยธานี", "อุบลราชธานี"
            ];

            const input = document.getElementById('province');
            const autocompleteList = document.getElementById('autocomplete-list');

            input.addEventListener('input', function() {
                const value = this.value;
                autocompleteList.innerHTML = ''; // ล้างรายการแนะนำก่อนหน้า
                if (!value) return; // ถ้าค่าในช่องว่างอยู่ ให้ออกจากฟังก์ชัน

                const suggestions = provinces.filter(province => province.includes(value));
                suggestions.forEach(suggestion => {
                    const div = document.createElement('div');
                    div.textContent = suggestion;
                    div.classList.add('autocomplete-suggestion');
                    div.addEventListener('click', function() {
                        input.value = suggestion; // ตั้งค่าฟิลด์ input ให้เป็นคำที่เลือก
                        autocompleteList.innerHTML = ''; // ล้างรายการแนะนำ
                    });
                    autocompleteList.appendChild(div);
                });
            });

            // คลิกที่อื่นนอกจากช่อง input จะลบรายการแนะนำ
            document.addEventListener('click', function(e) {
                if (e.target !== input) {
                    autocompleteList.innerHTML = ''; // ล้างรายการแนะนำ
                }
            });
        </script>
        <label for="new_email">อีเมล์:</label>
        <input type="email" id="new_email" name="new_email" required>
        <br>
        <label for="new_is_manager">สิทธิ์:</label>
        <select id="new_is_manager" name="new_is_manager">
            <option value="0">ลูกค้าทั่วไป</option>
            <option value="1">ผู้จัดการ</option>
        </select>
        <br>
        <input type="submit" value="เพิ่มบัญชีผู้ใช้">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['edit_id'])) {
        $new_username = $_POST['new_username'];
        $new_password = $_POST['new_password'];
        $new_confirm_password = $_POST['new_confirm_password'];
        $new_firstname = $_POST['new_firstname'];
        $new_lastname = $_POST['new_lastname'];
        $new_gender = $_POST['new_gender'];
        $new_age = $_POST['new_age'];
        $new_province = $_POST['new_province'];
        $new_email = $_POST['new_email'];
        $new_is_manager = $_POST['new_is_manager'];

        if ($new_password === $new_confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (username, password, firstname, lastname, gender, age, province, email, is_manager) 
                       VALUES ('$new_username', '$hashed_password', '$new_firstname', '$new_lastname', '$new_gender', $new_age, '$new_province', '$new_email', $new_is_manager)";
            if ($conn->query($insert_sql) === TRUE) {
                echo "เพิ่มบัญชีผู้ใช้สำเร็จ!";
                header("Location: admin.php");
                exit();
            } else {
                echo "เกิดข้อผิดพลาด: " . $conn->error;
            }
        } else {
            echo "รหัสผ่านไม่ตรงกัน!";
        }
    }
    $conn->close();
    ?>