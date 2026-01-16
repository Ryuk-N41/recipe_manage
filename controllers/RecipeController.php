<?php 
require_once '../config/db.php';
require_once '../models/Recipe.php';

class RecipeController {

    private $recipeModel;

    public function __construct($conn){
        $this->recipeModel = new Recipe($conn);
    }

    /* ===================== RECIPES ===================== */

    public function getRecipes(){
        return $this->recipeModel->getAllRecipes();
    }

    public function getFilteredRecipes($cuisine_id = null, $meal_type_id = null, $search_term = null){
        return $this->recipeModel->getFilteredRecipes($cuisine_id, $meal_type_id, $search_term);
    }

    public function getRecipe($id){
        return $this->recipeModel->getRecipeById($id);
    }

    /* ===================== ADD RECIPE ===================== */

    public function addRecipe(
        $title,
        $description,
        $details,
        $servings,
        $cuisine_id,
        $meal_type_id,
        $image,
        $ingredients
    ){
        $image_path = null;

        if (!empty($image['name'])) {

            $target_dir = "../uploads/";
            $allowed_exts = ['jpg', 'jpeg', 'png'];

            $image_ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));

            if (!in_array($image_ext, $allowed_exts)){
                return "Invalid image format (jpg, jpeg, png only)";
            }

            if ($image['size'] > 5000000){
                return "Image size must be under 5MB";
            }

            $unique_name = uniqid() . '.' . $image_ext;

            // ✅ CORRECT URL PATH (case-sensitive)
            $image_path = '/Recipe_Management/uploads/' . $unique_name;

            if (!move_uploaded_file($image['tmp_name'], $target_dir . $unique_name)){
                return "Failed to upload image.";
            }
        }

        $recipe_id = $this->recipeModel->addRecipe(
            $title,
            $description,
            $details,
            $servings,
            $cuisine_id,
            $meal_type_id,
            $image_path
        );

        if (!$recipe_id){
            return "Failed to save recipe";
        }

        /* ===================== INGREDIENTS ===================== */

        if (!empty($ingredients['name'])) {

            $ingredient_data = [];

            for ($i = 0; $i < count($ingredients['name']); $i++) {

                if (
                    !empty($ingredients['name'][$i]) &&
                    !empty($ingredients['quantity'][$i]) &&
                    !empty($ingredients['unit'][$i])
                ) {
                    $ingredient_data[] = [
                        'name'     => $ingredients['name'][$i],
                        'quantity' => (float)$ingredients['quantity'][$i],
                        'unit'     => $ingredients['unit'][$i]
                    ];
                }
            }

            if (empty($ingredient_data)){
                return "At least one valid ingredient is required";
            }

            if (!$this->recipeModel->addIngredients($recipe_id, $ingredient_data)){
                return "Failed to save ingredients";
            }

        } else {
            return "At least one ingredient is required";
        }

        return true;
    }

    /* ===================== UPDATE RECIPE ===================== */

    public function updateRecipe(
        $id,
        $title,
        $description,
        $details,
        $servings,
        $cuisine_id,
        $meal_type_id,
        $image,
        $ingredients
    ){
        $image_path = null;

        if (!empty($image['name'])) {

            $target_dir = "../uploads/";
            $allowed_exts = ['jpg', 'jpeg', 'png'];

            $image_ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));

            if (!in_array($image_ext, $allowed_exts)){
                return "Invalid image format";
            }

            if ($image['size'] > 5000000){
                return "Image size must be under 5MB";
            }

            $unique_name = uniqid() . '.' . $image_ext;
            $image_path  = '/Recipe_Management/uploads/' . $unique_name;

            if (!move_uploaded_file($image['tmp_name'], $target_dir . $unique_name)){
                return "Failed to upload image.";
            }

        } else {
            $recipe = $this->getRecipe($id);
            $image_path = $recipe['image'];
        }

        $success = $this->recipeModel->updateRecipe(
            $id,
            $title,
            $description,
            $details,
            $servings,
            $cuisine_id,
            $meal_type_id,
            $image_path
        );

        if ($success && !empty($ingredients['name'])) {

            $ingredient_data = [];

            for ($i = 0; $i < count($ingredients['name']); $i++) {
                if (
                    !empty($ingredients['name'][$i]) &&
                    !empty($ingredients['quantity'][$i]) &&
                    !empty($ingredients['unit'][$i])
                ) {
                    $ingredient_data[] = [
                        'name'     => $ingredients['name'][$i],
                        'quantity' => (float)$ingredients['quantity'][$i],
                        'unit'     => $ingredients['unit'][$i]
                    ];
                }
            }

            if (empty($ingredient_data)){
                return "At least one valid ingredient is required";
            }

            if (!$this->recipeModel->addIngredients($id, $ingredient_data)){
                return "Failed to save ingredients";
            }
        }

        return $success ? true : "Failed to update recipe";
    }

    /* ===================== DELETE ===================== */

    public function deleteRecipe($id){
        return $this->recipeModel->deleteRecipe($id)
            ? true
            : "Failed to delete recipe";
    }

    /* ===================== RATINGS ===================== */

    public function submitRating($recipe_id, $user_id, $rating, $review){
        if (!is_numeric($rating) || $rating < 1 || $rating > 5){
            return "Rating must be between 1 and 5";
        }

        $review = $review ? trim($review) : null;

        return $this->recipeModel->addRating(
            $recipe_id,
            $user_id,
            $rating,
            $review
        ) ? true : "Failed to submit rating";
    }

    public function getRatings($recipe_id){
        return $this->recipeModel->getRatings($recipe_id);
    }

    public function getAverageRating($recipe_id){
        return $this->recipeModel->getAverageRating($recipe_id);
    }
}
?>
