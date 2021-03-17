<?php

//phpinfo();
/* with this above we can see server infos and such. looks useful */
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

    $previous_image = $article->image_file;
    if ($article->setImageFile($conn, null)) {
        //notice we set the filename to null so as to not replace but delete.
        if ($previous_image) {
            unlink("../uploads/$previous_image");
        }
    }

    if ($article->setImageFile($conn, $filename)) {
        Url::redirect("/admin/article.php?id={$article->id}");
    }

}

?>
<?php require '../includes/header.php';?>

<h2>Delete article image</h2>
<?php if ($article->image_file): ?>
            <img src="/uploads/<?=$article->image_file;?>">
        <?php endif;?>

<form method='post'>
<p>Are you sure?</p>
<button>Delete</button>
<a href="/edit-article.php?id=<?=$article->id;?>">Cancel</a>

</form>

<?php require '../includes/footer.php';?>
