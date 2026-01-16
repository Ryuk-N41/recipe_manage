<?php
session_start();

/* ---------- Admin Guard ---------- */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/db.php';
require_once '../controllers/RecipeController.php';

$recipeController = new RecipeController($conn);

/* ---------- Dropdown Data ---------- */
function loadOptions($conn, $table) {
    $data = [];
    $res = mysqli_query($conn, "SELECT id, name FROM $table");
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }
    return $data;
}

$cuisines  = loadOptions($conn, 'cuisines');
$mealTypes = loadOptions($conn, 'meal_types');

/* ---------- State ---------- */
$error = '';
$success = '';
$isEdit = false;
$recipe = null;

/* ---------- Handle Form Submission ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

    $ingredients = [
        'name'     => $_POST['ingredient_name'],
        'quantity' => $_POST['ingredient_quantity'],
        'unit'     => $_POST['ingredient_unit']
    ];

    $image = (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK)
        ? $_FILES['image']
        : null;

    if ($id) {
        $result = $recipeController->updateRecipe(
            $id,
            $_POST['title'],
            $_POST['description'],
            $_POST['details'],
            (int)$_POST['servings'],
            (int)$_POST['cuisine_id'],
            (int)$_POST['meal_type_id'],
            $image,
            $ingredients
        );
        $success = $result === true ? "Recipe updated successfully" : $result;
    } else {
        $result = $recipeController->addRecipe(
            $_POST['title'],
            $_POST['description'],
            $_POST['details'],
            (int)$_POST['servings'],
            (int)$_POST['cuisine_id'],
            (int)$_POST['meal_type_id'],
            $image,
            $ingredients
        );
        $success = $result === true ? "Recipe added successfully" : '';
        $error   = $result === true ? '' : $result;
    }
}

/* ---------- Edit Recipe ---------- */
if (isset($_GET['edit'])) {
    $recipe = $recipeController->getRecipe((int)$_GET['edit']);
    $isEdit = (bool)$recipe;
}

/* ---------- Delete Recipe ---------- */
if (isset($_GET['delete'], $_GET['id'])) {
    $res = $recipeController->deleteRecipe((int)$_GET['id']);
    if ($res === true) {
        $success = "Recipe deleted successfully";
        header("Refresh:1; url=admin_add_recipe.php");
    } else {
        $error = $res;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Recipe Panel</title>
<style>
/* ---------- GENERAL ---------- */
body{font-family:Arial;background:#f4f4f4;margin:0}
header{background:#222;color:#fff;padding:15px;display:flex;justify-content:space-between;align-items:center}
.container{max-width:900px;margin:30px auto}
.panel{background:#fff;padding:25px;border-radius:6px}
label{margin-top:10px;display:block;font-weight:bold}
input,select,textarea{width:100%;padding:10px;margin-top:5px}
textarea{height:120px}
form button{width:100%;padding:10px;margin-top:12px;background:#333;color:#fff;border:none;cursor:pointer}
form button:hover{background:#555}

/* ---------- INGREDIENTS ---------- */
.ing-row{
    display:flex;
    gap:6px;
    margin-top:8px;
    align-items:center;
}
.ing-row input, .ing-row select{
    flex:1;
}
.remove-btn{
    background:#d32f2f;
    color:#fff;
    border:none;
    padding:6px 10px;
    width:auto;
    cursor:pointer;
}
.remove-btn:disabled{
    background:#e57373;
    cursor:not-allowed;
}
.add-btn{
    background:#388e3c;
    color:#fff;
    margin-top:10px;
    cursor:pointer;
}

/* ---------- RECIPE LIST ---------- */
.recipe-item{
    display:flex;
    justify-content:space-between;
    padding:8px;
    border-bottom:1px solid #ddd;
    align-items:center;
}
.action button{
    width:auto;
    padding:6px 10px;
    margin-left:5px;
}
.edit{background:#1976d2;color:#fff}
.delete{background:#d32f2f;color:#fff}

/* ---------- MESSAGES ---------- */
.success{color:green}
.error{color:red}
</style>
</head>

<body>

<header>
<h2>Recipe Admin</h2>
<div>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> | <a href="login.php?action=logout" style="color:#fff">Logout</a></div>
</header>

<div class="container">
<div class="panel">

<h3><?= $isEdit ? 'Edit Recipe' : 'Add Recipe' ?></h3>

<?php if($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
<?php if($success): ?><p class="success"><?= $success ?></p><?php endif; ?>

<form method="post" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?= $recipe['id'] ?? '' ?>">

<label>Title</label>
<input name="title" required value="<?= $recipe['title'] ?? '' ?>">

<label>Description</label>
<input name="description" required value="<?= $recipe['description'] ?? '' ?>">

<label>Instructions</label>
<textarea name="details" required><?= $recipe['details'] ?? '' ?></textarea>

<label>Servings</label>
<input type="number" name="servings" min="1" value="<?= $recipe['servings'] ?? '' ?>">

<label>Cuisine</label>
<select name="cuisine_id">
<?php foreach($cuisines as $c): ?>
<option value="<?= $c['id'] ?>" <?= isset($recipe)&&$recipe['cuisine_id']==$c['id']?'selected':'' ?>>
<?= htmlspecialchars($c['name']) ?>
</option>
<?php endforeach; ?>
</select>

<label>Meal Type</label>
<select name="meal_type_id">
<?php foreach($mealTypes as $m): ?>
<option value="<?= $m['id'] ?>" <?= isset($recipe)&&$recipe['meal_type_id']==$m['id']?'selected':'' ?>>
<?= htmlspecialchars($m['name']) ?>
</option>
<?php endforeach; ?>
</select>

<label>Image</label>
<input type="file" name="image">

<h4>Ingredients</h4>
<div id="ingredientBox">
<div class="ing-row">
<input name="ingredient_name[]" placeholder="Name" required>
<input name="ingredient_quantity[]" type="number" step="0.01" required>
<select name="ingredient_unit[]">
<option>cups</option><option>grams</option><option>pieces</option>
</select>
<button type="button" class="remove-btn" disabled>X</button>
</div>
</div>

<button type="button" class="add-btn">Add Ingredient</button>
<button><?= $isEdit ? 'Update Recipe' : 'Save Recipe' ?></button>
</form>

<hr>

<h4>Existing Recipes (AJAX)</h4>
<div id="recipeContainer"></div>

</div>
</div>

<script>
/* ---------- INGREDIENTS DYNAMIC ---------- */
const box=document.getElementById('ingredientBox');

document.querySelector('.add-btn').onclick=()=>{
    const row=document.createElement('div');
    row.className='ing-row';
    row.innerHTML=`
        <input name="ingredient_name[]" placeholder="Name" required>
        <input name="ingredient_quantity[]" type="number" step="0.01" required>
        <select name="ingredient_unit[]">
            <option>cups</option><option>grams</option><option>pieces</option>
        </select>
        <button type="button" class="remove-btn">X</button>`;
    box.appendChild(row);
    bindRemove();
};

function bindRemove(){
    const rows=document.querySelectorAll('.ing-row');
    rows.forEach((r, i)=>{
        const btn=r.querySelector('.remove-btn');
        btn.disabled = rows.length === 1;
        btn.onclick = ()=>{ if(rows.length>1){ r.remove(); bindRemove(); } };
    });
}
bindRemove();

/* ---------- AJAX LOAD RECIPES ---------- */
function loadRecipes(){
    fetch('../api/recipes.php')
        .then(res => res.json())
        .then(json=>{
            const c=document.getElementById('recipeContainer');
            c.innerHTML='';
            json.data.forEach(r=>{
                c.innerHTML+=`
                <div class="recipe-item">
                    <span>${r.title}</span>
                    <div class="action">
                        <a href="?edit=${r.id}"><button class="edit">Edit</button></a>
                        <a href="?delete=1&id=${r.id}" onclick="return confirm('Delete this recipe?')">
                            <button class="delete">Delete</button>
                        </a>
                    </div>
                </div>`;
            });
        });
}
loadRecipes();
</script>

</body>
</html>
