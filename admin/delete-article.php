<?php

require '../includes/init.php';

$conn = require '../includes/db.php';

Auth::requireLogin();

if (isset($_GET['id'])) {

    $article = Article::getById($conn, $_GET['id']);

    if (!$article) {
        die("article not found");
    }
} else {
    die("id not supplied, article not found");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
/*  we are checking if the post method is being used for the purpose of deleting
this is to avoid using the get method which a reload of page could trigger and such
 */

    if ($article->delete($conn)) {
        Url::redirect("/admin/index.php");

    }
}

?>
<?php require '../includes/header.php';?>

<h2>Delete article</h2>

<form method="post">

    <p>Are you sure?</p>

    <button>Delete</button>
    <a href="/article.php?id=<?=$article->id;?>">Cancel</a>

</form>

<?php require '../includes/footer.php';?>
