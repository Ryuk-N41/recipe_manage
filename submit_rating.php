<?php
session_start();

header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../controllers/RecipeController.php';

$controller = new RecipeController($conn);

/* ---------- Basic Validation ---------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method'
    ]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'User not logged in'
    ]);
    exit;
}

/* ---------- Input ---------- */
$recipe_id = isset($_POST['recipe_id']) ? (int)$_POST['recipe_id'] : 0;
$rating    = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$review    = isset($_POST['review']) ? trim($_POST['review']) : '';

$user_id = (int)$_SESSION['user_id'];

/* ---------- Validation ---------- */
if ($recipe_id <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid rating data'
    ]);
    exit;
}

/* ---------- Submit Rating ---------- */
$result = $controller->submitRating(
    $recipe_id,
    $user_id,
    $rating,
    $review !== '' ? $review : null
);

if ($result !== true) {
    echo json_encode([
        'success' => false,
        'error' => $result
    ]);
    exit;
}

/* ---------- Return Updated Data ---------- */
$ratings = $controller->getRatings($recipe_id);
$average = $controller->getAverageRating($recipe_id);

echo json_encode([
    'success' => true,
    'average_rating' => $average ? number_format($average, 1) : 'N/A',
    'ratings' => $ratings
]);
