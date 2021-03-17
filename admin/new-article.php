<?php

require '../includes/init.php';

Auth::requireLogin();
/* checking if user is authorized to see use content. */
$article = new Article();

$category_ids = [];
/* we initialise the ids as empty array(as its a new article.)*/

$conn = require '../includes/db.php';
/* notice how we have the connection above methods where it would be used in order to require it right. */
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

    if ($article->create($conn)) {

        $article->setCategories($conn, $category_ids);
/* we set the article categories */
        Url::redirect("/admin/article.php?id={$article->id}");
        /* notice the curly braces how they encapsulate the value */
    }
}

?>
<?php require '../includes/header.php';?>

<h2>New article</h2>

<?php require 'includes/article-form.php';?>

<?php require '../includes/footer.php';?>
