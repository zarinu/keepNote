<?php
session_start();
if(empty($_SESSION['userName'])) {
    header("Location:/mingo/loginPage.php");
    exit;
}
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
if(!empty($_POST["workToDo"])) {
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->insert(['text' => $_POST["workToDo"], 'done' => false, 'user' => $_SESSION["userName"]]);
    $manager->executeBulkWrite('keepNote.task', $bulk);
}
if(!empty($_POST["done"])) {
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(['text' => $_POST["done"], 'done' => false, 'user' => $_SESSION["userName"]], ['$set' => ['done' => true]]);
    $manager->executeBulkWrite('keepNote.task', $bulk);
}
if(!empty($_POST["delete"])) {
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->delete(['text' => $_POST["nameText"], 'user' => $_SESSION["userName"]]);
    $manager->executeBulkWrite('keepNote.task', $bulk);
}
$query = new MongoDB\Driver\Query(['user' => $_SESSION["userName"]]);
$rows = $manager->executeQuery('keepNote.task', $query);

if(!empty($_POST['theField'])) {
    $collabFindUserQ = new MongoDB\Driver\Query(['userName' => $_POST['theField']]);
    $existUser = $manager->executeQuery('keepNote.users', $collabFindUserQ)->toArray();
    if(empty($existUser)) {
        echo '<script type="text/JavaScript"> 
            alert("User Not Found!");
            </script>'
        ;
    }
    else{
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert(['text' => $_POST["nameText"], 'done' => false, 'user' => $existUser[0]->userName]);
        $manager->executeBulkWrite('keepNote.task', $bulk);
        echo '<script type="text/JavaScript"> 
            alert("Secsessful Collaborate!");
            </script>'
        ;
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>new beautiful project!</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="./style.css?v=<?php echo time(); ?>" />
    </head>
    <body>
        <div id="myDIV" class="header">
        <h2>My To Do List</h2>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
            <input name="workToDo" type="text" id="myInput" placeholder="what do you want to do? . . .">
            <input type="submit" onclick="window.location.href=window.location.href" class="addBtn" value="add">
        </form>
        </div>

        <ul id="myUL">
        <?php
        foreach($rows as $row) {
        ?>  
            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="theForm" method="POST">
                <li>
                    <input type="hidden" name="nameText" value="<?php echo $row->text;?>" />
                    <input class="checkbox" type="checkbox" value="<?php echo $row->text;?>" name="done" <?php
                    if($row->done == true) {
                        echo 'checked';
                    }
                    ?>/>

                    <input type="text" value="<?php echo $row->text;?>"/>
                    <input class="btnCheckbox" type="submit" value="to Do it"/>
                    <input class="btnCheckbox" name="delete" type="submit" value="Delete"/>

                    <input type="hidden" name="theField" id="theField"/>
                    <input class="btnCheckbox" name="collaborate" type="submit" value="collaborate" onclick="window.open('smallPageCollab.php', 
                            'mylala', 
                            'width=300,height=250,left=520, top=250'); 
                    return false;" />
                </li>
            </form>
        <?php } ?>
        </ul>
        
    </body>
</html>
<script>
    function printResault(Sr) {
        alert (Sr);
    }
</script>
