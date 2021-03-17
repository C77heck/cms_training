<?php if (!empty($article->errors)) : ?>
    <!-- notice how we display the errors through html upon submission. -->

    <ul>
        <?php foreach ($article->errors as $error) : ?>
            <li><?= $error ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post" id='formArticle'>

    <div class="form-group">
        <label for="title">Title</label>
        <input class="form-control" name="title" id="title" placeholder="Article title" value="<?= htmlspecialchars($article->title); ?>">
        <!-- notice the inserting tags. doing the php one will not work. we use the above method to sanitize the input and
    avoid cross site scripting. also notice how we access the article values. from the object-->
    </div>

    <div class="form-group">
        <label for="content">Content</label>
        <textarea class="form-control" name="content" rows="4" cols="40" id="content" placeholder="Article content"><?= htmlspecialchars($article->content); ?></textarea>
        <!-- notice how we reinsert the values after a subbmission. this way when we log out potential errors the user gets to keep
    their input rather than retyping it... we use the above method to sanitize the input and
    avoid cross site scripting. -->
    </div>

    <div class="form-group">
        <label for="published_at">Publication date and time</label>
        <input class="form-control" type="text" name="published_at" id="published_at" value="<?= htmlspecialchars($article->published_at); ?>">
        <!-- this one doesn't work due to cross browser compatibility and the format does not
match the php set one. but there's not point in fixing it now. -->

    </div>

    <fieldset>
        <legend>Categories</legend>

        <?php foreach ($categories as $category) : ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="category[]" value="<?= $category['id'] ?>" id="category<?= $category['id'] ?>" <?php if (in_array($category['id'], $category_ids)) : ?>checked<?php endif; ?>>
                <!-- we got the category ids in the edit-article and then chcek it here agianst the category
                       current id. if it has it then we add the check attribute. -->
                <label for="category<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></label>
            </div>
        <?php endforeach; ?>
    </fieldset>

    <button>Save</button>

</form>