<?php

require '../includes/init.php';

Auth::requireLogin();

$conn = require '../includes/db.php';

$paginator = new Paginator($_GET['page'] ?? 1, 6, Article::getTotal($conn));
/*the string in the square bracket is the one after a question mark such as ?page=value
if we don't have the page in the url we default to this this could be used too. if the
first is true then its value will be used else the following one. we could have used the ternary too. */
$articles = Article::getPage($conn, $paginator->limit, $paginator->offset);

?>
<?php require '../includes/header.php'; ?>


<h2>Administration</h2>

<p><a href='new-article.php'>New article</a></p>


<?php if (empty($articles)) : ?>
    <!-- notice how we handle an empty array -->

    <p>No articles found.</p>
<?php else : ?>

    <table class='table'>
        <thead>
            <th>Title</th>
            <th>Puplished</th>
        </thead>
        <tbody>
            <?php foreach ($articles as $article) : ?>
                <tr>
                    <td>
                        <a href="article.php?id=<?= $article['id']; ?>"><?= htmlspecialchars($article['title']); ?></a>
                        <!-- notice how we add the  id of the sql file and use it to direct us to a page
                where we can grab the same data. in that page and use it for the logic we need. -->
                    </td>
                    <td>
                        <?php if ($article['published_at']) : ?>
                            <time><?= $article['published_at'] ?></time>
                        <?php else : ?>
                            Unpublished
                            <button class="publish" data-id="<?= $article['id'] ?>">Publish</button>
                            <!-- we use the sql id to bind its value to the button object -->
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <!-- notice how we end the foreach method. -->
        </tbody>
    </table>

    <?php require '../includes/pagination.php'; ?>
<?php endif; ?>
<!-- notice how  we end the if block -->

<?php require '../includes/footer.php'; ?>