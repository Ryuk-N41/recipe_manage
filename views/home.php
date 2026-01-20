<?php
session_start();

require_once '../config/db.php';
require_once '../controllers/RecipeController.php';

$recipeCtrl = new RecipeController($conn);

// Fetch cuisine and meal type options
$cuisineQuery = mysqli_query($conn, "SELECT id, name FROM cuisines");
$cuisines = mysqli_fetch_all($cuisineQuery, MYSQLI_ASSOC);

$mealQuery = mysqli_query($conn, "SELECT id, name FROM meal_types");
$mealTypes = mysqli_fetch_all($mealQuery, MYSQLI_ASSOC);

// Admin statistics
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM users"))['cnt'];
$totalRecipes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM recipes"))['cnt'];
$totalReviews = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM ratings"))['cnt'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Recipe Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
        }
        .header {
            background: #333;
            color: #fff;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
            margin-bottom: 25px;
        }
        .card {
            background: #2c3e50;
            color: #fff;
            border-radius: 10px;
            padding: 18px;
            text-align: center;
            transition: transform 0.2s;
        }
        .card:hover { transform: translateY(-5px); }
        .filter-box {
            background: #fff;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .filter-box label { margin-right: 8px; }
        .filter-box select, .filter-box input { padding: 8px; margin-right: 10px; border-radius: 5px; }
        .filter-box button {
            padding: 8px 18px;
            border: none;
            background: #333;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }
        .filter-box button:hover { background: #555; }
        .recipe-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 18px;
        }
        .recipe-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            text-align: center;
            padding: 12px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .recipe-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .recipe-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 6px;
        }
        .error-msg { color: red; font-size: 14px; margin-bottom: 8px; }
        a.button {
            display: inline-block;
            background: rgb(97,4,4);
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            transition: background 0.2s;
        }
        a.button:hover { background: rgba(93,5,5,0.8); }
    </style>
</head>
<body>

<div class="header">
    <h1>Recipe Management</h1>
    <div>
        <?php if (!empty($_SESSION['username'])): ?>
            <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="login.php?action=logout" style="color:white; margin-left:10px;">Logout</a>
        <?php else: ?>
            <a href="login.php" style="color:white;">Login</a>
        <?php endif; ?>
    </div>
</div>

<div class="container">

    <!-- Admin Dashboard -->
    <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <div class="dashboard">
            <div class="card">Total Users<br><strong><?= $totalUsers ?></strong></div>
            <div class="card">Total Recipes<br><strong><?= $totalRecipes ?></strong></div>
            <div class="card">Total Reviews<br><strong><?= $totalReviews ?></strong></div>
        </div>

        <div style="text-align:center; margin-bottom: 30px;">
            <a href="admin_add_recipe.php" class="button">Manage Recipes</a>
        </div>
    <?php endif; ?>

    <!-- Recipe Filters -->
    <div class="filter-box">
        <form id="recipeFilterForm">
            <label for="cuisine">Cuisine:</label>
            <select name="cuisine_id" id="cuisine">
                <option value="">All</option>
                <?php foreach ($cuisines as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="meal">Meal Type:</label>
            <select name="meal_type_id" id="meal">
                <option value="">All</option>
                <?php foreach ($mealTypes as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="search">Search:</label>
            <input type="text" name="search_term" id="search" placeholder="e.g. Chicken Soup">

            <button type="submit">Apply Filters</button>
            <p id="formError" class="error-msg"></p>
        </form>
    </div>

    <!-- Recipe Grid -->
    <div class="recipe-grid" id="recipeGrid"></div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('recipeFilterForm');
    const grid = document.getElementById('recipeGrid');
    const errorBox = document.getElementById('formError');

    const fetchRecipes = async (filters = {}) => {
        const res = await fetch('get_recipes.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(filters)
        });
        const recipes = await res.json();

        grid.innerHTML = '';
        if (!recipes.length) {
            grid.innerHTML = '<p>No recipes found.</p>';
            return;
        }

        recipes.forEach(r => {
            grid.innerHTML += `
                <div class="recipe-card">
                    <a href="recipe_detail.php?id=${r.id}">
                        <img src="${r.image}" alt="${r.title}">
                        <h3>${r.title}</h3>
                        <p>${r.cuisine} - ${r.meal_type}</p>
                    </a>
                </div>`;
        });
    };

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        errorBox.textContent = '';

        const filters = {
            cuisine_id: form.cuisine.value,
            meal_type_id: form.meal.value,
            search_term: form.search.value.trim()
        };

        if (filters.search_term && filters.search_term.length < 2) {
            errorBox.textContent = "Search term must be at least 2 characters.";
            return;
        }

        fetchRecipes(filters);
    });

    fetchRecipes(); // initial load
});
</script>

</body>
</html>
