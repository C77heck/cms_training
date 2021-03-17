<?php

require 'includes/init.php';

$conn = require 'includes/db.php';

if (isset($_GET['id'])) {
    $article = Article::getWithCategories($conn, $_GET['id'], true);
    //we pass in the id and the connection

} else {
    $article = null;
}

?>
<?php require 'includes/header.php'; ?>

<?php if ($article) : ?>

    <article>
        <h2><?= htmlspecialchars($article[0]['title']); ?></h2>

        <time datetime="<?= $article[0]['published_at'] ?>">
            <!-- we add the same sql value into this attribute to make sure that the date is machine readable
         -->
            <?php
            $dateTime = new DateTime($article[0]['published_at']);
            echo $dateTime->format("j F, Y");
            /* notice how we grab sql date time string and use it as an argument to create a new
datetime object. which then we can use the regular methods and such. */
            ?>
        </time>
        <?php if ($article[0]['category_name']) : ?>
            <p>Categories:
                <?php foreach ($article as $a) : ?>
                    <?= htmlspecialchars($a['category_name']); ?>
                <?php endforeach; ?>
            </p>
        <?php endif; ?>

        <?php if ($article[0]['image_file']) : ?>
            <img src="/uploads/<?= $article[0]['image_file']; ?>">
        <?php endif; ?>

        <p><?= htmlspecialchars($article[0]['content']); ?></p>
    </article>

<?php else : ?>
    <!-- if no results were returned then we display below -->
    <p>Article not found.</p>

<?php endif; ?>

<?php require 'includes/footer.php'; ?>