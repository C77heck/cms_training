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
$category_ids = array_column($article->getCategories($conn), 'id');
/* we grab the category ids like so */
$categories = Category::getAll($conn);
//we get all the categories in an array like so
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    /* we using doulbe equal sign as in html we might have used lowercase post... */

    $article->title = $_POST['title'];
    $article->content = $_POST['content'];
    $article->published_at = $_POST['published_at'];
    /* they are assigned values from the post global. this way we can use the values without using a superglobal
    pointlessly multiply times. */
    $category_ids = $_POST['category'] ?? [];
    /* this is called a null coalesce operator. if we haven't checked any option then
    category does not exist in the post superglobal. */

    if ($article->update($conn)) {

        $article->setCategories($conn, $category_ids);
/* updating the categories */
        Url::redirect("/admin/article.php?id={$article->id}");
    }
}

?>
<?php require '../includes/header.php';?>

<h2>Edit article</h2>

<?php require 'includes/article-form.php';?>

<?php require '../includes/footer.php';?>
