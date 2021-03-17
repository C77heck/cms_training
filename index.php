<?php

require 'includes/init.php';

$conn = require 'includes/db.php';

$paginator = new Paginator($_GET['page'] ?? 1, 4, Article::getTotal($conn, true));
/*the string in the square bracket is the one after a question mark such as ?page=value
if we don't have the page in the url we default to this this could be used too. if the
first is true then its value will be used else the following one. we could have used the ternary too. */
$articles = Article::getPage($conn, $paginator->limit, $paginator->offset, true);

?>
<?php require 'includes/header.php'; ?>


<?php if (empty($articles)) : ?>
    <!-- notice how we handle an empty array -->

    <p>No articles found.</p>
<?php else : ?>

    <ul id='index'>
        <?php foreach ($articles as $article) : ?>
            <li>
                <article>
                    <h2><a href="article.php?id=<?= $article['id']; ?>"><?= htmlspecialchars($article['title']); ?></a></h2>
                    <!-- notice how we add the  id of the sql file and use it to direct us to a page
                where we can grab the same data. in that page and use it for the logic we need. -->

                    <time datetime="<?= $article['published_at'] ?>">
                        <!-- we add the same sql value into this attribute to make sure that the date is machine readable
         -->
                        <?php
                        $dateTime = new DateTime($article['published_at']);
                        echo $dateTime->format("j F, Y");
                        /* notice how we grab sql date time string and use it as an argument to create a new
datetime object. which then we can use the regular methods and such. */
                        ?>
                    </time>

                    <?php if ($article['category_names']) : ?>
                        <p>Categories:
                            <?php foreach ($article['category_names'] as $name) : ?>
                                <?= htmlspecialchars($name); ?>
                            <?php endforeach; ?>
                        </p>
                    <?php endif; ?>

                    <p><?= htmlspecialchars($article['content']); ?></p>
                </article>
            </li>
        <?php endforeach; ?>
        <!-- notice how we end the foreach method. -->

    </ul>

    <?php require 'includes/pagination.php'; ?>

<?php endif; ?>
<!-- notice how  we end the if block -->

<?php require 'includes/footer.php'; ?>