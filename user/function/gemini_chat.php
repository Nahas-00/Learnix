<?php
header('Content-Type: application/json');

// --- Configuration & Security ---
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Securely get API key from environment variable
$apiKey = 'AIzaSyA9O5DOkiJTMGLFJUMY0Xlq7fupDQP9qcE';

if (!$apiKey) {
    http_response_code(500); // Internal Server Error
    error_log('Error: GEMINI_API_KEY environment variable is not set.');
    echo json_encode(['reply' => 'Error: The server is not configured correctly.']);
    exit;
}

// --- Input Handling ---
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['prompt'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['reply' => 'Error: No prompt was provided.']);
    exit;
}

$prompt = $data['prompt'];

// Block requests for full code
if (preg_match('/(full code|write a program|complete program|solve this|solution)/i', $prompt)) {
    echo json_encode(['reply' => 'Please ask for a concept or a hint, not a full program.']);
    exit;
}


// --- API Call with cURL ---
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$apiKey";

$payload = json_encode([
    "contents" => [
        [
            "parts" => [
                ["text" => "You are a helpful assistant that only gives hints and concept explanations. Do not write full programs.\n\nUser: $prompt"]
            ]
        ]
    ]
]);

// Initialize cURL session
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
curl_setopt($ch, CURLOPT_POST, true);           // Set request method to POST
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload); // Attach the JSON payload
curl_setopt($ch, CURLOPT_HTTPHEADER, [          // Set headers
    'Content-Type: application/json',
    'Content-Length: ' . strlen($payload)
]);

// Execute cURL request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get HTTP status code
curl_close($ch); // Close cURL session

// --- Response Handling ---
if ($response === false || $httpCode !== 200) {
    http_response_code(502); // Bad Gateway
    error_log("Gemini API request failed with HTTP code: $httpCode. Response: $response");
    echo json_encode(['reply' => 'Error: Failed to communicate with the AI service.']);
    exit;
}

// Decode the API response and send it back to the client
$json = json_decode($response, true);
$reply = $json['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, I could not generate a response.';

echo json_encode(['reply' => $reply]);

?>