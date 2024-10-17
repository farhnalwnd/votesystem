<?php include 'includes/regist_modal.php'; 
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "votesystem"; // Nama database

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Memeriksa apakah data POST ada
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['firstname']) && isset($_POST['lastname'])) {
    // Mengambil data dari formulir
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];

    // Menangani upload foto
    $photo = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photoName = $_FILES['photo']['name'];
        $photoTmpName = $_FILES['photo']['tmp_name'];
        $photoSize = $_FILES['photo']['size'];
        $photoError = $_FILES['photo']['error'];
        $photoType = $_FILES['photo']['type'];
        
        // Validasi ekstensi foto
        $allowed = array('jpg', 'jpeg', 'png');
        $photoExt = strtolower(pathinfo($photoName, PATHINFO_EXTENSION));
        if (in_array($photoExt, $allowed)) {
            if ($photoSize < 5000000) { // Maksimal ukuran file 5MB
                $photoNewName = uniqid('', true) . "." . $photoExt;
                $photoDestination = 'uploads/' . $photoNewName;
                if (move_uploaded_file($photoTmpName, $photoDestination)) {
                    $photo = $photoNewName;
                } else {
                    echo "Gagal mengunggah foto.";
                    exit();
                }
            } else {
                echo "Ukuran foto terlalu besar.";
                exit();
            }
        } else {
            echo "Ekstensi foto tidak diperbolehkan.";
            exit();
        }
    }

    // Query untuk menyimpan data ke database
    $sql = "INSERT INTO admin (username, password, firstname, lastname, photo) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $user, $pass, $firstname, $lastname, $photo);

    if ($stmt->execute()) {
        echo "Pendaftaran berhasil!";
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Data formulir tidak lengkap.";
}

$conn->close();
?>

