SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `dbhw10` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `dbhw10`;

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(001, 'Rolex', '2024-10-14 14:28:51'),
(002, 'Omega', '2024-10-14 15:25:54'),
(003, 'GUCCi', '2024-10-14 15:50:04'),
(004, 'SEIKO', '2024-10-15 14:21:56'),
(005, 'BLVGARI', '2024-10-15 14:36:25'),
(006, 'CASIO', '2024-10-15 14:56:30');


CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `products` (`id`, `name`, `category`, `image`, `price`, `stock_quantity`, `details`) VALUES
(101, 'GMT-Master II', 'Rolex', 'img/GMT-Master II.png',406200.00, 10, 'เหมาะสำหรับการเดินทางท่องโลกเป็นอย่างยิ่ง เพราะเปี่ยมไปด้วยความทนทานและฟังก์ชันการทำงานชั้นเลิศ'),
(201, 'Datejust 36', 'Rolex', 'img/datejust 36.png', 343700.00, 8, 'นาฬิการุ่นนี้ออกแบบมาโดยเน้นเพิ่มความสง่างามแก่ผู้สวมใส่ โดยประกอบไปด้วยหน้าปัดไข่มุกขาว ประดับทองและเพชร ที่ผสมผสานการออกแบบได้อย่างลงตัว สะท้อนความงดงามเหนือกาลเวลา'),
(301, 'Submariner', 'Rolex', 'img/Submariner.png', 347600.00, 5, 'Submariner ถูกรังสรรค์ขึ้นเพื่อการสำรวจโลกใต้น้ำและการดำน้ำโดยเฉพาะ เมื่อเวลาผ่านไปมันได้พิสูจน์แล้วว่าสามารถสวมใส่ได้ในทุกโอกาส เป็นนาฬิกาที่นักสำรวจ นักกีฬา นักสร้างภาพยนตร์ ศิลปิน และบุคคลอีกมากมายเลือกสวมใส่'),
(401, 'Day-Date 40', 'Rolex', 'img/Day-Date 40.png', 1640300.00, 3, 'Day-Date 40 เหมาะสำหรับนักธุรกิจและนักสะสมที่ต้องการนาฬิกาหรูหราพร้อมฟังก์ชันแสดงวันและวันที่ในโอกาสพิเศษต่างๆ'),
(102, 'Seamaster Aqua Terra 150M', 'Omega', 'img/AQUA TERRA 150M.png',481000.00, 5, 'Seamaster Aqua Terra 150M เป็นนาฬิกาหรูจาก Omega ที่กันน้ำได้ถึง 150 เมตร เหมาะสำหรับนักเดินทางและผู้ที่ชื่นชอบการออกแบบทันสมัย สามารถใช้ได้ในทุกโอกาส'),
(202, 'SEAMASTER 300', 'Omega', 'img/SEAMASTER 300.png', 249000.00, 10, 'Seamaster 300 เป็นนาฬิกาดำน้ำจาก Omega ที่มีดีไซน์คลาสสิกและฟังก์ชันการใช้งานที่ยอดเยี่ยม เหมาะสำหรับนักดำน้ำและผู้ที่ชื่นชอบสไตล์ย้อนยุคที่สามารถใช้งานได้ในทุกโอกาส'),
(302, 'SEAMASTER DIVER 300M', 'Omega', 'img/diver 300 m.png', 219000.00, 10, 'Seamaster Diver 300M เป็นนาฬิกาดำน้ำที่มีความสามารถกันน้ำถึง 300 เมตรจาก Omega มีดีไซน์ที่ทันสมัยและฟังก์ชันการใช้งานที่หลากหลาย เหมาะสำหรับนักกีฬาทางน้ำและผู้ที่ชื่นชอบนาฬิกาที่มีประสิทธิภาพสูงในทุกสถานการณ์'),
(402, 'SEAMASTER PLANET OCEAN 6000M', 'Omega', 'img/PLANET OCEAN 6000M.png', 468000.00, 5, 'Seamaster Planet Ocean 6000M เป็นนาฬิกาดำน้ำจาก Omega ที่กันน้ำได้ถึง 6000 เมตร มีฟังก์ชันที่ครบครันและดีไซน์ที่แข็งแกร่ง เหมาะสำหรับนักดำน้ำมืออาชีพและผู้ที่ชื่นชอบกิจกรรมทางน้ำในสภาพแวดล้อมที่ท้าทาย'),
(103, 'GUCCI DIVE', 'GUCCI', 'img/GUCCI DIVE.png',91000.00, 20, 'ด้วยดีไซน์อันโดดเด่นที่ได้แรงบันดาลใจจากนาฬิกาสำหรับนักดำน้ำ ครั้งนี้ Gucci Dive นำเสนอด้วยหน้าปัดสีเงินพร้อมรูปผึ้งและสายนาฬิกาเหล็กสตีล ด้วยแรงบันดาลใจจากผลงานดั้งเดิม คอลเล็กชั่นล่าสุดสร้างสรรค์สัญลักษณ์อันเป็นเอกลักษณ์ขึ้นใหม่ด้วยจิตวิญญาณแบบร่วมสมัย'),
(203, 'GUCCI INTERLOCKING', 'GUCCI', 'img/GUCCI INTERLOCKING.png', 104000.00, 15, 'ด้วยการขับเคลื่อนระบบอัตโนมัติ นาฬิกา Gucci Interlocking โฉมใหม่ผสมผสานสัญลักษณ์ดั้งเดิมของแบรนด์ ดีไซน์ตัวเรือนขนาด 41 มม. มาพร้อมหน้าปัดสีดำพร้อมขอบหน้าปัด PVD สีดำ แอคเซสเซอรี่ชิ้นนี้สมบูรณ์แบบด้วยสายนาฬิกายางสีดำพร้อมเอฟเฟ็กต์ผ้า ตกแต่งด้วยสัญลักษณ์ Interlocking G ซิกเนเจอร์ในหน้าปัดวินาทีขนาดเล็ก'),
(303, 'G-TIMELESS', 'GUCCI', 'img/G-TIMELESS.png', 91000.00, 10, 'ลุคเสื้อที่ดูหรูหราของฤดูกาลนี้ครอบคลุมถึงนาฬิกา จิวเวลรี่ ซึ่งรวมถึงคอลเล็กชั่น G-Timeless ด้วย นาฬิกาทรงกลมเรือนนี้โดดเด่นด้วยหน้าปัดแบบเรียบหรูพร้อมด้วย Interlocking G, ตัวอักษร Gucci และลายอันเป็นเอกลักษณ์ที่ดูประณีตละเอียดอ่อน แอคเซสเซอรี่ชิ้นนี้สมบูรณ์แบบด้วยสายนาฬิกาเหล็กสตีล พร้อมตกแต่งด้วยหน้าปัดที่สองขนาดเล็ก'),
(403, 'GUCCI 25H', 'GUCCI', 'img/GUCCI 25H.png', 174000.00, 5, 'รูปทรงเพรียวบางของ GUCCI 25H ได้รับแรงบันดาลใจจากรูปแบบที่ทันสมัยของสถาปัตยกรรมร่วมสมัย นาฬิกาได้รับการนำเสนอด้วยตัวเรือนแบบบางหลายชั้นและสายนาฬิกาเหล็กสตีลห้าข้อ');


INSERT INTO `products` (`id`, `name`, `category`, `image`, `price`, `stock_quantity`, `details`) VALUES
(104, 'PRESAGE SHARP EDGED SERIES รุ่น SPB221J', 'SEIKO', 'img/SPB221J-1.jpg',57400.00, 10, 'ลวดลาย ASANOHA บนหน้าปัดจากยุคเฮอันที่มีมากว่า 1,000 ปีกลไก Cal.6R64 ขึ้นลานอัตโนมัติหรือเลือกขึ้นลานผ่านเม็ดมะยมเข็มแสดงเวลา GMT มาพร้อมสเกลรอบเวลา 24 ชั่วโมงบนขอบตัวเรือนหน้าปัดย่อยแสดงลานคงเหลือในตำแหน่ง 9 นาฬิกาสำรองลานได้นานสูงสุดถึง 45 ชั่วโมงตัวเรือนและสาย Stainless Steel แข็งแรงทนทานมาพร้อมบานพับแบบปลดล็อคด้วยปุ่มกดหน้าปัดย่อยแสดงวันที่ในตำแหน่ง 6 นาฬิกากระจกหน้าปัด Sapphire Crystal ป้องกันรอยขีดข่วนมาพร้อมการเคลือบสารตัดแสงสะท้อนบริเวณพื้นผิวด้านในกระจกฝาหลังแบบขันเกลียวโชว์กลไกการทำงานประสิทธิภาพการกันน้ำ 100 เมตรตัวเรือนขนาด 42.2 มิลลิเมตร ความหนา 13.65 มิลลิเมตร"'),
(204, 'Astron GPS Solar รุ่น SSH065J', 'SEIKO', 'img/SSH065J_1.jpg', 103900.00, 8, 'ฟังก์ชันปฏิทิน (วันและวันที่)หน้าปัดด้านล่างสามารถปรับเป็นโซนเวลาที่สองเข็มหลักสามารถปรับได้ด้วยตนเองตามโซนเวลาต่างๆตอบรับกับธีมสีน้ำเงินที่มาแรงสุด ๆ ในปีนี้ ทำให้แอสตรอนรุ่นใหม่ล่าสุดภายใต้รหัส SSH065J มีหน้าปัดที่สวยงามและโดดเด่น มีการเล่นลวดลายที่หน้าปัดย่อยให้ตัดกับหน้าปัดหลัก และยังมาพร้อมเครื่องระบบ GPS Solar คาลิเบอร์ 5X53 ที่พร้อมมอบความแม่นยำเหนือระดับในทุกๆ แห่งบนโลกใบนี้ และจะคอยจัดการเวลาให้คุณในทุก ๆ ไทม์โซน '),
(304, 'SEIKO 5 SPORTS AUTOMATIC รุ่น SRPD76K', 'SEIKO', 'img/SRPD76K_1.jpg', 16500.00, 5, 'จากแรงบันดาลใจแห่งความสำเร็จ 5 ทศวรรษที่ผ่านมา และการออกแบบนั้นก็เริ่มจากพื้นฐานของนาฬิกาไซโกทรงสปอร์ตที่ได้รับความชื่นชอบมากที่สุด รังสรรค์ตัวเรือนด้วยวัสดุสเตนเลสสตีลชุบสีโรสโกลด์ ใช้เข็มนาทีทรงปลายลูกศร หลักชั่วโมงที่ชัดเจน และขอบตัวเรือนหมุนได้ทิศทางเดียว เครื่องกลไกคาลิเบอร์ 4R36 สามารถขึ้นลานด้วยมือได้ สำรองพลังงานได้นาน 41 ชั่วโมง และแฮ็คเข็มวินาที่เมื่อตั้งเวลา'),
(404, 'SEIKO 5 SPORTS AUTOMATIC รุ่น SRPD59K', 'SEIKO', 'img/SRPD59K_1.jpg', 13300.00, 3, 'ไซโก 5 สปอร์ตจะเป็นหนึ่งในตัวเลือกที่ลงตัวสำหรับการมิกซ์แอนด์แมทช์ให้กับเสื้อผ้าสุดคูลชองคุณดูมีสไตล์ที่แตกต่าง ด้วยหน้าปัดสีส้มสะดุดตา ขอบตัวเรือนสีดำ และสเตนเลสสตีลที่ทนทาน แถมยังสะดวกต่อการใช้งานด้วยเครื่องระบบอัตโนมัติ คาลิเบอร์ 4R36 ที่มาพร้อมฟังก์ชั่นบอกวันและวันที่สะดวกต่อการใช้งานในชีวิตประจำวัน '),
(105, 'OCTO ROMA WATCH', 'BLVGARI', 'img/BLVGARI1.png',332000.00, 5, 'Octo Roma Chronograph watch with mechanical manufacture movement, automatic winding and chronograph functions, satin-brushed and polished stainless steel case and interchangeable bracelet, blue Clous de Paris dial. Water-resistant up to 100 metres.'),
(205, 'BVLGARI BVLGARI WATCH', 'BLVGARI', 'img/BLVGARI2.png', 1591000.00, 5, 'Bvlgari Bvlgari Tubogas watch with 18 kt yellow gold case and double logo engraving on the bezel, black lacquered dial, diamond indexes, and double-spiral mini Tubogas bracelet in 18 kt yellow, white and rose gold. Water-resistant up to 50 metres.'),
(305, 'DIVAS DREAM WATCH', 'BLVGARI', 'img/BLVGARI3.png', 1082000.00, 5, 'SDivas Dream watch with 18 kt rose gold case set with brilliant-cut diamonds marquetry dial set with red wood elements and brilliant-cut diamonds and red alligator bracelet. Water-resistant up to 30 metres'),
(405, 'BVLGARI ALUMINIUM X FENDER® WATCH', 'BLVGARI', 'img/BLVGARI4.png', 154000.00, 5, 'Bvlgari Aluminium GMT x Fender® watch with mechanical movement, automatic winding, GMT 24h function, 40 mm aluminium case, brown rubber bezel and bracelet, and brown and cream gradient dial. Limited Edition of 1.200 pieces'),
(106, 'W-218HM-5BV', 'CASIO', 'img/W-218HM-5BV.png',1100.00, 20, 'ฟังก์ชันที่ใช้งานได้จริงและจอแสดงผลที่โดดเด่นมาบรรจบกันในดีไซน์สปอร์ตแบบดิจิตอล W-218H มาพร้อมแบตเตอรี่ 7 ปี กันน้ำลึกได้ 50 เมตร และไฟ LED ที่ช่วยให้คุณรับรู้เวลาได้เสมอแม้จะอยู่ในที่มืด'),
(206, 'ABL-100WEG-9A', 'CASIO', 'img/ABL-100WEG-9A.png', 4800.00, 15, 'แฟชั่นวินเทจที่ผสานรวมกับฟังก์ชันยุคปัจจุบัน ส่งมอบสไตล์ย้อนยุคที่มาพร้อมกับคุณสมบัติที่ทันสมัยดีไซน์สุดคลาสสิกที่มีกลิ่นอายของวันวาน ด้วยตัวเรือนแบบเรียบง่ายที่จับคู่กับสายนาฬิกาโลหะแบบหลายแถบ พร้อมโครงสร้างที่วางเรียบลงไปเพื่อให้แน่ใจถึงความสบายในการสวมใส่'),
(306, 'MTP-B170-5EV', 'CASIO', 'img/MTP-B170-5EV.png', 2700.00, 10, 'การออกแบบแนวอะนาล็อกสุดโดดเด่นพร้อมด้วยความอเนกประสงค์ที่สามารถจับคู่ได้กับทุกโอกาส ตั้งแต่งานแบบทางการไปจนถึงการไปเที่ยวแบบสบายๆนาฬิกาทรงสี่เหลี่ยมอันงดงามนี้โดดเด่นด้วยตัวเรือนสีทองหรือสีพิงค์โกลด์เพื่อความสวยงามสะดุดตาการกันน้ำสูงสุด 50 เมตรทำให้คุณหมดกังวลแม้ในวันที่มีฝน'),
(406, 'MTP-M305D-7A2V', 'CASIO', 'img/MTP-M305D-7A2V.png', 4500.00, 15, 'เดินทางไปสู่ดวงจันทร์—ติดตามข้างขึ้นข้างแรมของดวงจันทร์ด้วยนาฬิกาอะนาล็อกที่โดดเด่นด้วยหน้าปัดย่อยหลายหน้า นาฬิกานี้นำแฟชั่นอันมีสไตล์และดีไซน์ดวงจันทร์สุดคลาสสิกมารวมเข้าไว้ด้วยกัน เลือกในแบบชอบและเตรียมพร้อมสำหรับทุกช่วงเวลา การกันน้ำลึก 50 เมตรจะทำให้คุณหมดกังวลเมื่อล้างเครื่องครัวหรือเจอกับฝน');



CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `age` int(11) NOT NULL,
  `province` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `is_manager` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);


ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
COMMIT;


CREATE TABLE `cart_items` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `quantity` int(11) NOT NULL DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `cart_items`
    ADD PRIMARY KEY (`id`),
    ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE;

ALTER TABLE `cart_items`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

