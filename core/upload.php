<?php
if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $tempFile = $_FILES['file']['tmp_name'];
    $targetPath = '../assets/images';
    $targetFile = $targetPath . basename($_FILES['file']['name']);

    // Move uploaded file to target path
    if (move_uploaded_file($tempFile, $targetFile)) {
        echo json_encode([
            'success' => true,
            'message' => 'File uploaded successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to move uploaded file.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'File upload error: ' . $_FILES['file']['error']
    ]);
}
?>
