<?php
declare(strict_types = 1);
include '../../src/bootstrap.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); //Get and validate id
$image = [];

if(!$id) {
    redirect('admin/articles.php', ['failure' => 'Article not found']); //Redirect

}

$article = $cms->getArticle()->get($id, false); //Get article
if(!$article['image_file']) {                   //if no article
    redirect('admin/article.php', ['id' => $id]); // Redirect
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {          //If form was submitted
    $path = APP_ROOT . '/public/uploads/' . $article['image_file']; // Path to file
    $cms->getArticle()->imageDelete($article['image_id'], $path, $id); //Delete image
    redirect('admin/article.php', ['id' => $id]);               //Redirect

}

?>

<?php include APP_ROOT . '/public/includes/admin-header.php' ?>
<main class="container admin" id="content">
    <form action="image-delete.php?id=<?= $id ?>" method="POST" class="narrow">
        <h1>Delete Image</h1>
        <p><img src="../uploads/<?= html_escape($article['image_file'])?>"
            alt="<?= html_escape($article['image_alt']) ?>"></p>
        <p>Click confirm to delete the image:</p>
        <input type="submit" name="delete" value="Confirm" class="btn btn-primary" />
        <a href="article.php?id=<?= $id ?>" class="btn btn-danger">Cancel</a>
    </form>
</main>
<?php include APP_ROOT . '/public/includes/admin-footer.php'; ?>
