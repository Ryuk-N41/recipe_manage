<?php
session_start();

require_once '../config/db.php';
require_once '../controllers/RecipeController.php';

$recipeCtrl = new RecipeController($conn);

/* ---------- Fetch Recipe ---------- */
$recipeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$recipeData = $recipeCtrl->getRecipe($recipeId);

if (!$recipeData) {
    exit('Recipe does not exist.');
}

/* ---------- Logged User ---------- */
$currentUser = $_SESSION['user_id'] ?? 0;

/* ---------- Add New Timer ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['timer_minutes'])) {
    if ($currentUser > 0) {
        $label = trim($_POST['timer_label']) ?: 'Step';
        $minutes = (int)$_POST['timer_minutes'];

        if ($minutes > 0) {
            $seconds = $minutes * 60;
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO timers (user_id, recipe_id, duration, label)
                 VALUES (?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "iiis", $currentUser, $recipeId, $seconds, $label);
            mysqli_stmt_execute($stmt);
            header("Location: ".$_SERVER['REQUEST_URI']);
            exit;
        }
    }
}

/* ---------- Delete Timer ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_timer'])) {
    $timerId = (int)$_POST['remove_timer'];
    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM timers WHERE id = ? AND user_id = ? AND recipe_id = ?"
    );
    mysqli_stmt_bind_param($stmt, "iii", $timerId, $currentUser, $recipeId);
    mysqli_stmt_execute($stmt);
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit;
}

/* ---------- Load Timers ---------- */
$userTimers = [];
if ($currentUser) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, label, duration FROM timers
         WHERE user_id = ? AND recipe_id = ?
         AND created_at > DATE_SUB(NOW(), INTERVAL 1 DAY)"
    );
    mysqli_stmt_bind_param($stmt, "ii", $currentUser, $recipeId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $userTimers[] = $row;
    }
}

/* ---------- Ratings ---------- */
$allRatings = $recipeCtrl->getRatings($recipeId);
$avgRating  = $recipeCtrl->getAverageRating($recipeId);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($recipeData['title']) ?> | Recipe</title>

<style>
body{font-family:Arial,sans-serif;background:#f0f2f5;margin:0}
header{background:#2c3e50;color:#fff;padding:15px 30px;display:flex;justify-content:space-between}
.container{max-width:1200px;margin:25px auto;padding:0 30px}
.card{background:#fff;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,.1);margin-bottom:25px}
.card h2{text-align:center;padding:15px;margin:0}
.flex{display:flex;gap:20px}
img{max-width:100%;border-radius:10px}
.btn{display:inline-block;padding:10px 18px;background:#3498db;color:#fff;border-radius:5px;text-decoration:none}
.btn.danger{background:#e74c3c}
.section{background:#f9f9f9;padding:15px;border-radius:8px;margin-top:20px}
.timer-row{display:flex;align-items:center;gap:10px;margin-bottom:10px}
.timer-controls button{padding:5px 10px;border:none;border-radius:5px;background:#2c3e50;color:#fff}
.delete{background:#e74c3c}
textarea{width:100%;min-height:60px}
</style>
</head>

<body>

<header>
    <h1>Recipe Management</h1>
    <div>
        <?php if (!empty($_SESSION['username'])): ?>
            Welcome, <?= htmlspecialchars($_SESSION['username']) ?> |
            <a href="login.php?action=logout" style="color:#fff">Logout</a>
        <?php else: ?>
            <a href="login.php" style="color:#fff">Login</a>
        <?php endif; ?>
    </div>
</header>

<div class="container">
<a href="home.php" class="btn danger">Back</a>

<div class="card">
<h2><?= htmlspecialchars($recipeData['title']) ?></h2>

<div class="flex">
    <div style="flex:1">
        <img src="<?= htmlspecialchars($recipeData['image']) ?>">
    </div>

    <div style="flex:2">
        <p><b>Cuisine:</b> <?= htmlspecialchars($recipeData['cuisine']) ?></p>
        <p><b>Meal:</b> <?= htmlspecialchars($recipeData['meal_type']) ?></p>
        <p><b>Servings:</b> <?= htmlspecialchars($recipeData['servings']) ?></p>
        <p><?= htmlspecialchars($recipeData['description']) ?></p>

        <h3>Ingredients</h3>
        <?php if (!empty($recipeData['ingredients'])): ?>
            <ul>
                <?php foreach ($recipeData['ingredients'] as $ing): ?>
                    <li><?= htmlspecialchars($ing['quantity'].' '.$ing['unit'].' '.$ing['name']) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No ingredients listed.</p>
        <?php endif; ?>

        <a onclick="window.print()" class="btn">Print</a>

        <div class="section">
            <h3>Ratings</h3>
            <p><b>Average:</b> <?= $avgRating ? number_format($avgRating,1) : 'N/A' ?>/5</p>

            <?php if ($currentUser): ?>
                <form onsubmit="submitRating(event)">
                    <select name="rating" required>
                        <option value="">Rating</option>
                        <?php for($i=1;$i<=5;$i++): ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                    <textarea name="review" placeholder="Optional review"></textarea>
                    <button class="btn">Submit</button>
                </form>
            <?php else: ?>
                <p><a href="login.php">Login</a> to rate.</p>
            <?php endif; ?>

            <!-- ✅ FIXED WARNING HERE -->
            <ul id="ratingList">
                <?php foreach ($allRatings as $r): ?>
                    <li>
                        <b><?= htmlspecialchars($r['username']) ?>:</b>
                        <?= (int)$r['rating'] ?>/5
                        <?php if (isset($r['review']) && trim($r['review']) !== ''): ?>
                            <p><?= htmlspecialchars($r['review']) ?></p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
</div>

<div class="section">
<h3>Timers</h3>

<?php foreach ($userTimers as $t): ?>
<div class="timer-row" data-id="<?= $t['id'] ?>" data-time="<?= $t['duration'] ?>">
    <span id="display_<?= $t['id'] ?>"><?= gmdate("i:s",$t['duration']) ?></span>
    <?= htmlspecialchars($t['label']) ?>
    <div class="timer-controls">
        <button onclick="startTimer(<?= $t['id'] ?>)">Start</button>
        <button onclick="pauseTimer(<?= $t['id'] ?>)">Pause</button>
        <button onclick="resetTimer(<?= $t['id'] ?>)">Reset</button>
    </div>
    <form method="post">
        <input type="hidden" name="remove_timer" value="<?= $t['id'] ?>">
        <button class="delete">Delete</button>
    </form>
</div>
<?php endforeach; ?>

<?php if ($currentUser): ?>
<form method="post">
    <input type="text" name="timer_label" placeholder="Step name">
    <input type="number" name="timer_minutes" min="1" required>
    <button class="btn">Add Timer</button>
</form>
<?php endif; ?>
</div>

<script>
const timers={};

document.querySelectorAll('[data-time]').forEach(el=>{
    const id=el.dataset.id;
    timers[id]={total:+el.dataset.time,left:+el.dataset.time,run:null};
});

function render(id){
    const m=Math.floor(timers[id].left/60);
    const s=timers[id].left%60;
    document.getElementById('display_'+id).innerText=
        String(m).padStart(2,'0')+':'+String(s).padStart(2,'0');
}

function startTimer(id){
    if(timers[id].run) return;
    timers[id].run=setInterval(()=>{
        if(timers[id].left>0){
            timers[id].left--;render(id);
        } else clearInterval(timers[id].run);
    },1000);
}

function pauseTimer(id){
    clearInterval(timers[id].run);
    timers[id].run=null;
}

function resetTimer(id){
    pauseTimer(id);
    timers[id].left=timers[id].total;
    render(id);
}

async function submitRating(e){
    e.preventDefault();
    const f=e.target;
    const data=new URLSearchParams({
        recipe_id:<?= $recipeId ?>,
        rating:f.rating.value,
        review:f.review.value
    });
    await fetch('submit_rating.php',{method:'POST',body:data});
    location.reload();
}
</script>

</body>
</html>
