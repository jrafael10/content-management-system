<?php
declare(strict_types = 1);                      //Use strict types
include '../../src/bootstrap.php';
$deleted = null;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); //Validate id
if(!$id) {
    redirect('articles.php', ['failure' => 'Article not found']); //Redirect with error
}

$article =$cms->getArticle()->get($id, false);

if (!$article) {                                          // If $article empty
    redirect('articles.php', ['failure' => 'Article not found']); // Redirect
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {          //If form was submitted
    /*try {
        $pdo->beginTransaction();                   //Start transaction

        // If there is an image, delete the image first
        if($article['image_id']) {                                          // If there was an image
            $sql = "UPDATE article SET image_id = null WHERE id = :article_id;"; //SQL to update article table
            pdo($pdo, $sql, [$id] );                                        //Remove image from article
            $sql = "DELETE FROM image WHERE id = :id;";                     //SQL to delete from image table
            pdo($pdo, $sql, [$article['image_id']]);                        //Delete from image table
            $path = '../uploads/' .$article['image_file'];                  //Set the image path
            if(file_exists($path)) {                                        //If image file exists
                $unlink = unlink($path);                                // DElete image file
            }
        }
        $sql = "DELETE FROM article WHERE id = :id;";            //SQL to delete article
        pdo($pdo, $sql, [$id]);                                 //Delete article
        $pdo->commit();                                         //Commit transaction
        redirect('articles.php', ['success' => 'Article deleted']); //Redirect
    } catch (PDOException $e) {                                 //If exception thrown
        $pdo->rollBack();                                       //Roll back SQL changes
        throw $e;                                               //Re-throw exception
    }
    */

    if(isset($article['image_id'])) {                                 // If there was an image
        $path = APP_ROOT . '/public/uploads/' . $article['image_file'];  //Set the image path
        $cms->getArticle()->imageDelete($article['image_id'], $path, $id);  //Delete image
    }
    $deleted = $cms->getArticle()->delete($id);             //Delete article
    if($deleted  === true) {
        redirect('admin/articles.php', ['success' => 'Article deleted']); // Redirect
    } else {
        throw new Exception('Unable to delete article');        //Throw an exception
    }
}



?>
<?php include APP_ROOT . '/public/includes/admin-header.php'; ?>

<main class="container admin" id="content">
    <form action="article-delete.php?id=<?= $id ?>" method="POST" class="narrow">
        <h1>Delete Article</h1>
        <p>Click confirm to delete the article: <em><?= html_escape($article['title']) ?></em></p>
        <input type="submit" name="delete" value="Confirm" class="btn btn-primary">
        <a href="articles.php" class="btn btn-danger">Cancel</a>
    </form>

</main>

<?php include APP_ROOT . '/public/includes/admin-footer.php'; ?>






