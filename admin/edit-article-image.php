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

    try {

        if (empty($_FILES)) {
            throw new Exception('Invalid upload');
        }

        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_OK:
                break;

            case UPLOAD_ERR_NO_FILE:
                throw new Exception('No file uploaded');
                break;

            case UPLOAD_ERR_INI_SIZE:
                throw new Exception('File is too large (from the server settings)');
                break;

            default:
                throw new Exception('An error occurred');
        }

        // Restrict the file size
        if ($_FILES['file']['size'] > 1000000) {
/* file size restriction */
            throw new Exception('File is too large');

        }
/* mime type validation below.  */
        $mime_types = ['image/gif', 'image/png', 'image/jpeg'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['file']['tmp_name']);
/* we check what the file type is. tmp_name is a temporary name given inside the array
hence the way of accessing itthis way */
        if (!in_array($mime_type, $mime_types)) {

            throw new Exception('Invalid file type');

        }
        //move the file to the uploads folder
        $pathinfo = pathinfo($_FILES["file"]["name"]);
        //we get the path info
        $base = $pathinfo['filename'];
        //we grab the filename
        $base = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);
        //we sanitize it from invalid characters like so.
        $base = mb_substr($base, 0, 200);
        //we make sure the name isn't longer than 200 chars
        $filename = $base . "." . $pathinfo['extension'];
        //we add back the original extension
        $destination = "../uploads/$filename";
        //then we set the destination folder
        $i = 1;

        while (file_exists($destination)) {
            $filename = $base . "-{$i}" . "." . $pathinfo['extension'];
            $destination = "../uploads/$filename";
            $i++;
            /* if the filename already exist we ammend it like so */
        }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {

            //replacing images
            $previous_image = $article->image_file;
            if ($article->setImageFile($conn, $filename)) {
                if ($previous_image) {
                    unlink("../uploads/$previous_image");
                }
            }

            if ($article->setImageFile($conn, $filename)) {
                Url::redirect("/admin/article.php?id={$article->id}");
            }
        } else {
            throw new Exception('Unable to move uploaded file');
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    /* we wrap the switch statement in try catch to handle the exception it would throw
we are creating one so it's obvious why. there are multiple type of errors we could get but
we just handle the most common. we look into the File superglobal array for errors
and obviously we break out of the block. */
}

?>
<?php require '../includes/header.php';?>

<h2>Edit article image</h2>
<?php if ($article->image_file): ?>
            <img src="/uploads/<?=$article->image_file;?>">
            <a class='delete' href="delete-article-image.php?id=<?=$article->id;?>">Delete</a>
        <?php endif;?>

<?php if (isset($error)): ?>
    <p><?=$error?>
    <?php endif;?>



<form method='post' enctype='multipart/form-data'>
<div>
    <label for='file'>Image file</label>
    <input type='file' name='file' id='file'>
</div>
<button>Upload</button>
</form>

<?php require '../includes/footer.php';?>
