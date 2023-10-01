<?php
// setup
declare(strict_types=1);
use Cms\Validate\Validate;
include '../../src/bootstrap.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); // Get id and validate

$category = [
  'id' => $id,
  'name' => '',
  'description' => '',
  'navigation' => false,
];                                  //Initialize category array

$errors = [
  'warning' => '',                  //Initialize errors array
  'name' => '',
  'description' => '',
];

// If there was an id, page is editing the category, so get current category
if($id) {                                               //If got an id
                                                         //SQL statement
    $category = $cms->getCategory()->get($id);           //Get category data

    if(!$category) {                                    //If no category found
        redirect('categories.php', ['failure' => 'Category not found']); //Redirect with error
    }
}

//Part B: Get and validate form data
if($_SERVER['REQUEST_METHOD'] == 'POST') {  //If form submitted
    $category['name'] = $_POST['name'];  //Get name
    $category['description'] = $_POST['description']; //Get description
    $category['navigation'] = (isset($_POST['navigation']) and ($_POST['navigation'] == 1)) ? 1 : 0; //Get navigation

    // Check if all data is valid and create error messages if it is invalid
    $errors['name'] = (Validate::isText($category['name'], 1, 24)) ? '': 'Name should be 1-24 characters.'; //Validate name
    $errors['description'] =(Validate::isText($category['description'], 1, 254)) ? '' : 'Description should be 1-254 characters.'; // Validate description

    $invalid = implode($errors);                //Join error messages

   // echo $invalid;
    //PART C: Check if data is valid, if so update database
    if($invalid) {                                          //If data is invalid
        $errors['warning'] = 'Please correct errors';       //Create error messages
    } else {                                                //Otherwise
        $arguments = $category;                             //Set arguments array for SQL
        if($id){                                            //If there is an id
           $saved = $cms->getCategory()->update($arguments); //Try to update category
        } else {                                            //If there is no id
            unset($arguments['id']);                        //Remove id from category array
            $saved = $cms->getCategory()->create($arguments); //Try to create category
        }

        if($saved === true) {
            redirect('admin/categories.php', ['success' => 'Category saved']); //Redirect
        }

        if($saved === false) {                                  //If duplicate category
            $errors['warning'] = 'Category name already in use';        //Store error message
        }
    }
}


?>

<?php include '../includes/admin-header.php'; ?>
<main class="container admin" id="content">
    <form action="category.php?id=<?= $id ?>" method="post" class="narrow">
        <?php if ($id){ ?>
        <h1>Edit Category</h1>
        <?php } else { ?>
        <h1>Add Category</h1>
        <?php } ?>
        <?php if($errors['warning']) { ?>
            <div class="alert alert-danger"><?= $errors['warning'] ?></div>
        <?php } ?>

        <div class="form-group">
            <label for="name">Name: </label>
            <input type="text" name="name" id="name"
                    value="<?= html_escape($category['name']) ?>" class="form-control">
            <span class="errors"><?= $errors['name'] ?></span>
        </div>

        <div class="form-group">
            <label for="description">Description: </label>
            <textarea name="description" id="description"
                class="form-control"><?= html_escape($category['description']) ?></textarea>
            <span class="errors"><?= $errors['description'] ?></span>
        </div>

        <div class="form-check">
            <input type="checkbox" name="navigation" id="navigation"
                    value="1" class="form-check-input"
            <?=($category['navigation'] === 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="navigation">Navigation</label>
        </div>
        <input type="submit" value="Save" class="btn btn-primary btn-save">
    </form>
</main>
<?php include '../includes/admin-footer.php'; ?>
