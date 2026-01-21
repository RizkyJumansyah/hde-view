<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetDir = "uploads/";
    
    // Pastikan folder ada
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $clientName = $_POST['name'] ?? 'Guest';
    $category = $_POST['category'] ?? 'General';
    $serialNumber = $_POST['sn'] ?? '-';
    $issue = $_POST['issue'] ?? '-';

    $fileName = time() . '_' . basename($_FILES["file"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileUrl = "https://" . $_SERVER['HTTP_HOST'] . "/" . $targetFilePath;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        
        // --- LOGIC KIRIM EMAIL ---
        $to = "alamat-email-anda@gmail.com"; // GANTI DENGAN EMAIL ANDA
        $subject = "Tiket Bantuan Baru: " . $clientName;
        
        $message = "
        <html>
        <head>
            <title>Tiket Bantuan HDe Technology</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6;'>
            <h2 style='color: #1d4ed8;'>Laporan Kendala Baru</h2>
            <table border='0' cellpadding='10'>
                <tr><td><strong>Nama/Instansi:</strong></td><td>$clientName</td></tr>
                <tr><td><strong>Kategori:</strong></td><td>$category</td></tr>
                <tr><td><strong>Serial Number:</strong></td><td>$serialNumber</td></tr>
                <tr><td><strong>Kendala:</strong></td><td>$issue</td></tr>
                <tr><td><strong>Link File:</strong></td><td><a href='$fileUrl'>Lihat Dokumentasi</a></td></tr>
            </table>
            <br>
            <hr>
            <p style='font-size: 11px; color: #666;'>Dikirim otomatis dari sistem HDe Technology Support.</p>
        </body>
        </html>
        ";

        // Header untuk email HTML
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";

        // Eksekusi kirim email
        mail($to, $subject, $message, $headers);

        echo json_encode([
            "status" => "success", 
            "fileUrl" => $fileUrl
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal upload."]);
    }
}
?>