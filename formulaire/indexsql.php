<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Merci Bryan

function attribution($origine) {
    if (isset($_POST[$origine]) && !empty($_POST[$origine])) {
        return $_POST[$origine];
    }
}
try {
    $db = new PDO ('mysql:host=localhost;dbname=todolist;charset=utf8', 'root', 'root');
} catch (Exception $e) {
    print_r("Erreur:" .$e->getMessage());
    }

$data = attribution('add');
$sanitize = filter_var($data, FILTER_SANITIZE_STRING);
$der = $db->query('SELECT tache FROM trololist WHERE id = (SELECT max(id) FROM trololist)');
$varder = $der->fetch();
//envoyer dans les taches
if (!empty($sanitize) && isset($sanitize) && $sanitize != $varder['tache']) {
  $db->query('INSERT INTO trololist (tache, archive) VALUES ("'.$sanitize.'", "false")');
}
//archiver les taches
$archive = $db->query('SELECT tache FROM trololist WHERE archive = "false"');
if (isset($_POST['archiver'])&& isset($_POST['task'])){
    for ($i = 0 ; $i < count($_POST['task']); $i++){
        $db->exec('UPDATE trololist SET archive = "true" WHERE tache = "'.$_POST['task'][$i].'"');
        }
}
//supprimer les taches
if(isset($_POST['delete']) &&  isset($_POST['supp'])) {
    for ($a = 0; $a < count($_POST['supp']); $a++) {
        $db->exec('DELETE FROM trololist WHERE tache = "'.$_POST['supp'][$a].'"');
    }
}
$tache = $db->query('SELECT tache FROM trololist WHERE archive = "false"');
$archive = $db->query('SELECT tache FROM trololist WHERE archive = "true"');
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="style.css">
        <meta charset="utf-8">
        <link href="https://fonts.googleapis.com/css?family=Amatic+SC" rel="stylesheet">
        <title>To Do List</title>
    </head>
    <body>
        <section class="page">
            <h1>Liste des tâches</h1>
            <form class="afaire" action="indexsql.php" method="post">
              <section class="ajouter">
                  <h4>Ajouter une tâche</h4>
                  <input type="text" name="add" value="">
                  <input type="submit" class="send" name="submit" value="envoyer">
              </section>
                <section class="effect">
                    <h4>Tâches à effectuer</h4>
                    <?php
                        while ($vartache = $tache->fetch()) {
                            echo '<label class="list"><input type="checkbox" name="task[]" value="'.$vartache['tache'].'">'.$vartache['tache'].'</label><br/>';
                        }
                    ?>
                    <input type="submit" name="archiver" value="archiver">
                </section>
                <section class="archive">
                    <h4>Archiver</h4>
                    <?php
                        while ($vararch = $archive->fetch()){
                            echo '<label class = "line"><input type="checkbox" name="supp[]" value="'.$vararch['tache'].'">'.$vararch['tache'].'</label><br/>';
                        }
                    ?>
                    <input type="submit" name="delete" value="supprimer">
                </section>
            </form>
        </section>
    </body>
</html>
