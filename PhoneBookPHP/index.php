<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$computers = simplexml_load_file("kniga.xml");
$counter = 0;
$date_today = date("m.d.y");


function searchByName($query){
    global $computers;
    $result = array();
    foreach ($computers -> kontakt as $computer){
        if (substr(strtolower($computer -> nimi), 0, strlen($query))==strtolower($query))
            array_push($result, $computer);
    }
    return $result;
}

function searchByNumber($query){
    global $computers;
    $result = array();
    foreach ($computers -> kontakt as $computer){
        if (substr(strtolower($computer -> telefon), 0, strlen($query))==strtolower($query))
            array_push($result, $computer);
    }
    return $result;
}

function searchBySurname($query){
    global $computers;
    $result = array();
    foreach ($computers -> kontakt as $computer){
        if (substr(strtolower($computer -> perekonnanimi), 0, strlen($query))==strtolower($query))
            array_push($result, $computer);
    }
    return $result;
}

function searchByEmail($query){
    global $computers;
    $result = array();
    foreach ($computers -> kontakt as $computer){
        if (substr(strtolower($computer -> email), 0, strlen($query))==strtolower($query))
            array_push($result, $computer);
    }
    return $result;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Telefoniraamat</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="skeleton.css">
</head>
<body>
<h1>Telefoniraamat</h1>

<table border="1">
    <tr>
        <th>Nimi</th>
        <th>Perenimi</th>
        <th>Telefon</th>
        <th>E-mail</th>
        <th> </th>

    </tr>
    <?php

    foreach($computers -> kontakt as $arvuti) {
        echo "<tr>";
        echo "<td>".($arvuti -> nimi)."</td>";
        echo "<td>".($arvuti -> perekonnanimi)."</td>";
        echo "<td>".($arvuti -> telefon)."</td>";
        echo "<td>".($arvuti -> email)."</td>";
        ?>
        <td><a href="index.php?id=<?php echo $arvuti['id']; ?> ">Muuda</a></td>
        <?php
        echo "</tr>";
    }

    ?>
</table>
<br />

<?php
if(isset($_POST['submitSave'])){
    foreach($computers -> kontakt as $arvuti){
        if($arvuti['id'] == $_POST['id']){
            $arvuti -> telefon = $_POST['telefon'];
			$arvuti -> nimi = $_POST['nimi'];
			$arvuti -> perekonnanimi = $_POST['perekonnanimi'];
			$arvuti -> email = $_POST['email'];
            break;
        }
    }
    file_put_contents('kniga.xml', $computers->asXML());
    //header('location:index.php');
}

foreach($computers -> kontakt as $arvuti){
    if($arvuti['id'] == $_GET['id']){
        $id = $arvuti['id'];
        $nimi = $arvuti -> nimi;
        $perenimi = $arvuti -> perekonnanimi;
        $number = $arvuti->telefon;
		$email = $arvuti -> email;
        break;
    }
}

if(isset($_POST['submitSave1'])) {
    $human = $computers->addChild('kontakt');
    $human->addAttribute('id', $_POST['id']);
    $human->addChild('nimi', $_POST['nimi']);
    $human->addChild('perekonnanimi', $_POST['perekonnanimi']);
    $human->addChild('telefon', $_POST['telefon']);
    $human->addChild('email', $_POST['email']);
    file_put_contents('kniga.xml', $computers->asXML());
    header('location:index.php');
}
?>
<form method="post">
    <table cellpading="2" cellspacing="2">
            <input type="hidden" name="id" value="<?php echo $id; ?>" readonly="readonly">
        <tr>
            <td>Nimi:</td>
            <td><input type="text" name="nimi" value="<?php echo $nimi; ?>"></td>
        </tr>
        <tr>
            <td>Perenimi:</td>
            <td><input type="text" name="perekonnanimi" value="<?php echo $perenimi; ?>"></td>
        </tr>
        <tr>
            <td>Telefoni number:</td>
            <td><input type="text" name="telefon" value="<?php echo $number; ?>"></td>
        </tr>
		<tr>
            <td>Email:</td>
            <td><input type="text" name="email" value="<?php echo $email; ?>"></td>
        </tr>
        <tr>
			<td><input type="submit" value="Save" name="submitSave1"></td>
            <td><input type="submit" value="Muuda" name="submitSave"></td>
        </tr>
    </table>
</form>

<form method="post">
    Search: <input type="text" name="search"/>
    Otsing nimi järgi<input type="radio" name="radiofind"  value="name" checked>
    Otsing perekonnanimi järgi<input type="radio" name="radiofind"  value="surname">
    Otsing telefoni numbri järgi<input type="radio" name="radiofind" value="num">
    Otsing emaili järgi<input type="radio" name="radiofind" value="email">
    <input type="submit" value="Find" />
</form>

<table border="1">
    <tr>
        <th>Nimi</th>
        <th>Perenimi</th>
        <th>Telefon</th>
        <th>E-mail</th>

    </tr>
    <?php

    if(!empty($_POST["search"])){
        $answer = $_POST['radiofind'];
        if ($answer == "name"){
            $result = searchByName($_POST["search"]);
        }
        else if($answer == "num"){
            $result = searchByNumber($_POST["search"]);
        }
        else if($answer == "surname"){
            $result = searchBySurname($_POST["search"]);
        }
        else if($answer == "email"){
            $result = searchByEmail($_POST["search"]);
        }


        foreach($result as $arvuti) {
            $counter++;
            echo "<tr>";
            echo "<td>".($arvuti -> nimi)."</td>";
            echo "<td>".($arvuti -> perekonnanimi)."</td>";
            echo "<td>".($arvuti -> telefon)."</td>";
            echo "<td>".($arvuti -> email)."</td>";

            echo "</tr>";
        }
        echo "Leitud ".($counter)." kontakti";
    }
    ?>
</table>
</body>
</html>