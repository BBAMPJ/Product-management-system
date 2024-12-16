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

function showAlert($message)
{
    echo "<script>alert('$message');</script>";
}

// ฟังก์ชันสำหรับเข้าสู่ระบบ
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['username'] === 'admin' && $_POST['password'] === '1234') {
        $_SESSION['username'] = 'admin';
        $_SESSION['isAdmin'] = true;
        header("Location: admin.php");
        exit();
    }
    if ($_POST['username'] === 'manage' && $_POST['password'] === '1234') {
        $_SESSION['username'] = 'manager';
        $_SESSION['is_manager'] = true;
        header("Location: manager.php");
        exit();
    }
}


// ตรวจสอบการเข้าสู่ระบบของ user ธรรมดา
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ตรวจสอบว่าเข้ามาจาก index.html
    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'index.html') !== false) {
        // เงื่อนไขสำหรับผู้ที่เข้ามาจาก index.html

        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $result = $conn->query("SELECT * FROM users WHERE username='$username'");
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if ($user['is_manager'] == 1) {
                    $_SESSION['is_manager'] = true;
                    header("Location: manager.php"); // ส่งผู้ใช้ไปที่ manager.php
                    exit();
                } else if (password_verify($password, $user['password'])) {
                    $_SESSION['username'] = $username;
                    $_SESSION['isAdmin'] = false;
                    $_SESSION['user_id'] = $user['id'];
                    header("Location: main.php");
                    exit();
                } else {
                    $_SESSION['error_message'] = "รหัสผ่านไม่ถูกต้อง";
                    // แสดง popup เมื่อกลับไปที่ index.html
                    echo "<script>alert('รหัสผ่านไม่ถูกต้อง'); window.location.href='index.html';</script>";
                    exit();
                }
            } else {
                $_SESSION['error_message'] = "ชื่อผู้ใช้ไม่ถูกต้อง";
                // แสดง popup เมื่อกลับไปที่ index.html
                echo "<script>alert('ชื่อผู้ใช้ไม่ถูกต้อง'); window.location.href='index.html';</script>";
                exit();
            }
        }
    }
}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'])) {
    // ตรวจสอบว่าเข้ามาจาก regis.html
    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'regis.html') !== false) {

        // รับข้อมูลจากฟอร์ม
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $gender = $_POST['gender'];
        $age = $_POST['age'];
        $province = $_POST['province'];
        $email = $_POST['email'];

        // ตรวจสอบว่ารหัสผ่านและการยืนยันรหัสผ่านตรงกัน
        if ($password !== $confirm_password) {
            echo "<script>alert('รหัสผ่านไม่ตรงกัน'); window.location.href='regis.html';</script>";
            exit();
        }

        // ตรวจสอบความยาวรหัสผ่านและข้อจำกัด
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            echo "<script>alert('รหัสผ่านต้องมีอย่างน้อย 8 ตัว ประกอบด้วยตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก และตัวเลขอย่างน้อยหนึ่งตัว'); window.location.href='regis.html';</script>";
            exit();
        }

        // ตรวจสอบว่ามีชื่อผู้ใช้ซ้ำในฐานข้อมูล
        $username_check_sql = "SELECT * FROM users WHERE username='$username'";
        $username_check_result = $conn->query($username_check_sql);

        if ($username_check_result->num_rows > 0) {
            echo "<script>alert('ชื่อผู้ใช้นี้ถูกใช้งานแล้ว กรุณาใช้ชื่อผู้ใช้ใหม่'); window.location.href='regis.html';</script>";
            exit();
        }

        // ตรวจสอบว่ามีอีเมลซ้ำในฐานข้อมูล
        $email_check_sql = "SELECT * FROM users WHERE email='$email'";
        $email_check_result = $conn->query($email_check_sql);

        if ($email_check_result->num_rows > 0) {
            echo "<script>alert('อีเมลนี้ถูกใช้งานแล้ว กรุณาใช้เมลอื่น'); window.location.href='regis.html';</script>";
            exit();
        }

        // แฮชรหัสผ่านก่อนบันทึก
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // เพิ่มข้อมูลผู้ใช้ใหม่ในฐานข้อมูล
        $insert_sql = "INSERT INTO users (username, password, firstname, lastname, gender, age, province, email) 
                           VALUES ('$username', '$hashed_password', '$firstname', '$lastname', '$gender', $age, '$province', '$email')";

        if ($conn->query($insert_sql) === TRUE) {
            // แจ้งให้ทราบว่าสมัครบัญชีสำเร็จ
            $_SESSION['registration_success'] = true; // ใช้ session เพื่อจัดการการแจ้งเตือน
            echo "<script>alert('สมัครบัญชีผู้ใช้สำเร็จ!'); window.location.href='index.html';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด: " . $conn->error . "'); window.location.href='regis.html';</script>";
        }
    }
}

// แสดง popup เมื่อสมัครบัญชีสำเร็จ
if (isset($_SESSION['registration_success']) && $_SESSION['registration_success']) {
    echo "<script>alert('สมัครบัญชีสำเร็จ'); window.location.href='index.html';</script>";
    unset($_SESSION['registration_success']); // ลบ session เพื่อไม่ให้แสดงอีก
    exit();
}





// เช็คการเข้าสู่ระบบ
if (!isset($_SESSION['username'])) {
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
            <th>การจัดการ</th>
        </tr>";

if ($_SESSION['isAdmin']) {
    // ถ้าเป็น admin แสดงข้อมูลผู้ใช้ทั้งหมด
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
        header("Location: showdata.php");
        exit();
    }

    // ฟอร์มเพิ่มบัญชีผู้ใช้
?>
    <h3>เพิ่มบัญชีผู้ใช้</h3>
    <form action="" method="POST">
        <label for="new_username">ชื่อผู้ใช้:</label>
        <input type="text" id="new_username" name="new_username" required>
        <br>
        <label for="new_password">รหัสผ่าน:</label>
        <input type="password" id="new_password" name="new_password" required>
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
        <input type="number" id="new_age" name="new_age" min="1" required>
        <br>
        <label for="new_province">จังหวัด:</label>
        <input type="text" id="province" name="province" required autocomplete="off"><br>
        <div id="autocomplete-list" class="autocomplete-suggestions"></div>
        <option value="กรุงเทพมหานคร">กรุงเทพมหานคร</option>
        <option value="กระบี่">กระบี่</option>
        <option value="กรุงเทพมหานคร">กรุงเทพมหานคร</option>
        <option value="ชลบุรี">ชลบุรี</option>
        <option value="เชียงใหม่">เชียงใหม่</option>
        <option value="เชียงราย">เชียงราย</option>
        <option value="ตรัง">ตรัง</option>
        <option value="นครราชสีมา">นครราชสีมา</option>
        <option value="นครปฐม">นครปฐม</option>
        <option value="นครศรีธรรมราช">นครศรีธรรมราช</option>
        <option value="นราธิวาส">นราธิวาส</option>
        <option value="น่าน">น่าน</option>
        <option value="ปทุมธานี">ปทุมธานี</option>
        <option value="ประจวบคีรีขันธ์">ประจวบคีรีขันธ์</option>
        <option value="บุรีรัมย์">บุรีรัมย์</option>
        <option value="ปัตตานี">ปัตตานี</option>
        <option value="พะเยา">พะเยา</option>
        <option value="พังงา">พังงา</option>
        <option value="ภูเก็ต">ภูเก็ต</option>
        <option value="มหาสารคาม">มหาสารคาม</option>
        <option value="มุกดาหาร">มุกดาหาร</option>
        <option value="ยโสธร">ยโสธร</option>
        <option value="ร้อยเอ็ด">ร้อยเอ็ด</option>
        <option value="ลพบุรี">ลพบุรี</option>
        <option value="ลำปาง">ลำปาง</option>
        <option value="ลำพูน">ลำพูน</option>
        <option value="ศรีสะเกษ">ศรีสะเกษ</option>
        <option value="สกลนคร">สกลนคร</option>
        <option value="สงขลา">สงขลา</option>
        <option value="สุโขทัย">สุโขทัย</option>
        <option value="สุพรรณบุรี">สุพรรณบุรี</option>
        <option value="อำนาจเจริญ">อำนาจเจริญ</option>
        <option value="อุดรธานี">อุดรธานี</option>
        <option value="อุตรดิตถ์">อุตรดิตถ์</option>
        <option value="อุบลราชธานี">อุบลราชธานี</option>
        <option value="เชียงราย">เชียงราย</option>
        <option value="นครสวรรค์">นครสวรรค์</option>
        <option value="เพชรบุรี">เพชรบุรี</option>
        <option value="เพชรบูรณ์">เพชรบูรณ์</option>
        <option value="สมุทรปราการ">สมุทรปราการ</option>
        <option value="สมุทรสงคราม">สมุทรสงคราม</option>
        <option value="สุราษฎร์ธานี">สุราษฎร์ธานี</option>
        <option value="สระแก้ว">สระแก้ว</option>
        <option value="สระบุรี">สระบุรี</option>
        <option value="นครศรีธรรมราช">นครศรีธรรมราช</option>
        <option value="ระนอง">ระนอง</option>
        <option value="ระยอง">ระยอง</option>
        <option value="อุทัยธานี">อุทัยธานี</option>
        <option value="ยะลา">ยะลา</option>
        <option value="มุกดาหาร">มุกดาหาร</option>
        <option value="หนองคาย">หนองคาย</option>
        <option value="หนองบัวลำภู">หนองบัวลำภู</option>
        <option value="บึงกาฬ">บึงกาฬ</option>
        <option value="ชัยภูมิ">ชัยภูมิ</option>
        <option value="อ่างทอง">อ่างทอง</option>
        <option value="อยุธยา">อยุธยา</option>
        <option value="พิจิตร">พิจิตร</option>
        <option value="นครพนม">นครพนม</option>
        <option value="สิงห์บุรี">สิงห์บุรี</option>
        <option value="พะเยา">พะเยา</option>
        <option value="เพชรบูรณ์">เพชรบูรณ์</option>
        <option value="มุกดาหาร">มุกดาหาร</option>
        <option value="สุโขทัย">สุโขทัย</option>
        <option value="ตราด">ตราด</option>
        <option value="กำแพงเพชร">กำแพงเพชร</option>
        </select>
        <br>
        <label for="new_email">อีเมล์:</label>
        <input type="email" id="new_email" name="new_email" required>
        <br>
        <input type="submit" value="เพิ่มบัญชีผู้ใช้">
    </form>
    <?php

    // เพิ่มบัญชีผู้ใช้ใหม่
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_username'])) {
        $new_username = $_POST['new_username'];
        $new_password = $_POST['new_password'];
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $new_firstname = $_POST['new_firstname'];
        $new_lastname = $_POST['new_lastname'];
        $new_gender = $_POST['new_gender'];
        $new_age = $_POST['new_age'];
        $new_province = $_POST['new_province'];
        $new_email = $_POST['new_email'];

        $insert_sql = "INSERT INTO users (username, password, firstname, lastname, gender, age, province, email) 
                       VALUES ('$new_username', '$new_hashed_password', '$new_firstname', '$new_lastname', '$new_gender', $new_age, '$new_province', '$new_email')";
        if ($conn->query($insert_sql) === TRUE) {
            echo "เพิ่มบัญชีผู้ใช้สำเร็จ!";
            header("Refresh: 0");
        } else {
            echo "เกิดข้อผิดพลาด: " . $conn->error;
        }
    }
} else {
    // ถ้าเป็น user แสดงเฉพาะข้อมูลของตัวเอง
    $result = $conn->query("SELECT * FROM users WHERE id = " . $_SESSION['user_id']);
    if ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>1</td>
                <td>" . $row["id"] . "</td>
                <td>" . $row["username"] . "</td>
                <td>" . $row["firstname"] . "</td>
                <td>" . $row["lastname"] . "</td>
                <td>" . $row["gender"] . "</td>
                <td>" . $row["age"] . "</td>
                <td>" . $row["province"] . "</td>
                <td>" . $row["email"] . "</td>
                <td>
                    <a href='?edit_id=" . $row["id"] . "'>แก้ไข</a>
                </td>
              </tr>";
        echo "</table>";
    }
}

// ถ้ามีการแก้ไข
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $edit_result = $conn->query("SELECT * FROM users WHERE id=$edit_id");
    $edit_user = $edit_result->fetch_assoc();
    ?>
    <h3>แก้ไขข้อมูลผู้ใช้</h3>
    <form action="" method="POST">
        <input type="hidden" name="id" value="<?php echo $edit_user['id']; ?>">
        <label for="firstname">ชื่อ:</label>
        <input type="text" id="firstname" name="firstname" value="<?php echo $edit_user['firstname']; ?>" required>
        <br>
        <label for="lastname">นามสกุล:</label>
        <input type="text" id="lastname" name="lastname" value="<?php echo $edit_user['lastname']; ?>" required>
        <br>
        <label for="gender">เพศ:</label>
        <select id="gender" name="gender" required>
            <option value="male" <?php if ($edit_user['gender'] == 'male') echo 'selected'; ?>>ชาย</option>
            <option value="female" <?php if ($edit_user['gender'] == 'female') echo 'selected'; ?>>หญิง</option>
            <option value="other" <?php if ($edit_user['gender'] == 'other') echo 'selected'; ?>>อื่นๆ</option>
        </select>
        <br>
        <label for="age">อายุ:</label>
        <input type="number" id="age" name="age" value="<?php echo $edit_user['age']; ?>" min="1" required>
        <br>
        <label for="province">จังหวัด:</label>
        <select id="province" name="province" required>
            <option value="กรุงเทพมหานคร" <?php if ($edit_user['province'] == 'กรุงเทพมหานคร') echo 'selected'; ?>>กรุงเทพมหานคร</option>
            <option value="เชียงใหม่" <?php if ($edit_user['province'] == 'เชียงใหม่') echo 'selected'; ?>>เชียงใหม่</option>
            <option value="สงขลา" <?php if ($edit_user['province'] == 'สงขลา') echo 'selected'; ?>>สงขลา</option>
        </select>
        <br>
        <label for="email">อีเมล์:</label>
        <input type="email" id="email" name="email" value="<?php echo $edit_user['email']; ?>" required>
        <br>
        <input type="submit" value="อัปเดตข้อมูล">
    </form>
<?php
}

// ตรวจสอบการอัปเดตข้อมูล
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $province = $_POST['province'];
    $email = $_POST['email'];

    $update_sql = "UPDATE users SET firstname='$firstname', lastname='$lastname', gender='$gender', age=$age, province='$province', email='$email' WHERE id=$id";
    if ($conn->query($update_sql) === TRUE) {
        echo "ข้อมูลอัปเดตสำเร็จ!";
        header("Refresh: 0");
    } else {
        echo "เกิดข้อผิดพลาด: " . $conn->error;
    }
}

// ปุ่ม Logout
echo "<a href='index.html'>ออกจากระบบ</a>";

// ปิดการเชื่อมต่อ
$conn->close();
?>