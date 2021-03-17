<?php

require '../includes/init.php';

$conn = require '../includes/db.php';

Auth::requireLogin();

if (isset($_GET['id'])) {

    $article = Article::getWithCategories($conn, $_GET['id']);
    //we pass in the id and the connection
} else {
    $article = null;
}

?>
<?php require '../includes/header.php';?>

<?php if ($article): ?>

    <article>
        <h2><?=htmlspecialchars($article[0]['title']);?></h2>


    <?php if ($article[0]['published_at']): ?>
        <time><?=$article[0]['published_at']?></time>
    <?php else: ?>
        Unpublished
    <?php endif;?>


        <?php if ($article[0]['category_name']): ?>
            <p>Categories:
            <?php foreach ($article as $a): ?>

                <?=htmlspecialchars($a['category_name'])?>

                <?php endforeach;?>
                </p>
                <?php endif;?>

        <?php if ($article[0]['image_file']): ?>
            <img src="/uploads/<?=$article[0]['image_file'];?>">
        <?php endif;?>

        <p><?=htmlspecialchars($article[0]['content']);?></p>
    </article>

    <a href="edit-article.php?id=<?=$article[0]['id'];?>">Edit</a>
    <a class='delete' href="delete-article.php?id=<?=$article[0]['id'];?>">Delete</a>
    <a href="edit-article-image.php?id=<?=$article[0]['id'];?>">Edit image</a>
    <?php else: ?>
<!-- if no results were returned then we display below -->
    <p>Article not found.</p>

<?php endif;?>

<?php require '../includes/footer.php';?>
