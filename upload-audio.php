<?php

// Set the directory where you want to store the audio files
$uploadDirectory = 'uploads/';

// Ensure the 'uploads' directory exists
if (!file_exists($uploadDirectory)) {
    mkdir($uploadDirectory, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the 'audio' key is present in the POST request
    if (isset($_FILES['audio'])) {
        $audioFile = $_FILES['audio'];

        // Validate the uploaded file
        if ($audioFile['error'] === UPLOAD_ERR_OK) {
            $fileType = explode('/', $audioFile['type'])[1];

            // Ensure a valid file extension
            if (!$fileType || !in_array(strtolower($fileType), ['wav', 'mp3', 'ogg'])) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid file format']);
                exit;
            }

            $newFileName = uniqid('recording_') . '.' . $fileType;
            $uploadPath = $uploadDirectory . $newFileName;

            // Move the uploaded file to the server
            move_uploaded_file($audioFile['tmp_name'], $uploadPath);

            // Provide a link to access the saved recording
            $audioLink = $_SERVER['HTTP_ORIGIN'] . '/' . $uploadPath;

            echo json_encode(['status' => 'success', 'message' => 'File uploaded successfully', 'audio_link' => $audioLink]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error uploading file']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No audio file uploaded']);
    }
} else {
    // Respond with an error for non-POST requests
    header('HTTP/1.1 405 Method Not Allowed');
    echo 'Method Not Allowed';
}
?>
