<?php
session_start();
header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../controllers/RecipeController.php';

$controller = new RecipeController($conn);

$method = $_SERVER['REQUEST_METHOD'];

/* ---------- GET ---------- */
if ($method === 'GET') {

    if (!isset($_GET['id'])) {
        echo json_encode([
            'status' => 'success',
            'data'   => $controller->getRecipes()
        ]);
        exit;
    }

    $recipe = $controller->getRecipe((int)$_GET['id']);
    echo json_encode(
        $recipe
        ? ['status'=>'success','data'=>$recipe]
        : ['status'=>'error','message'=>'Recipe not found']
    );
    exit;
}
