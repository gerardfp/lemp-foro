<!DOCTYPE html>
<meta charset="UTF-8">
<title>foro</title>

<style>
body {
	font-family: sans;
    max-width: 80em;
    margin: auto;
}

h1 {
	background: linear-gradient(90deg, #e35614 70%, #fa1654);
	color: white;
	padding: 16px;
    margin-top: 0;
}

body > details {
	border: 1px solid #e35614;
	margin: 0px;
    margin-top: 12px;
}

details {
	margin: 12px 0px 12px 12px;
	border-left: 1px solid #e35614;
}

summary {
	background: linear-gradient(90deg, #fff5ed 70%, #ffe0d0);
	padding: 16px 16px 0px 16px;
}

.reply summary {
	list-style: none;
	padding: 4px;
	cursor: pointer;
	color: #e35614;
}

.reply {
	border: 0;
	padding: 4px;
	margin: 0;
}

input[type=submit] {
	background: #e35614;
    color: white;
    border: 1px solid #fa5624;
    padding: .3em;
}
</style>

<h1>&#128024; foro</h1>
<form action="?">
    <textarea type='text' name='post' rows='8' cols='80' ></textarea>
    <br>
	<input type="submit" value="Publicar">
</form>

<?php
$mysqli = new mysqli("mysql", "myuser", "mypass123", "mydb");

if(isset($_GET['post'])){
	$stmt = $mysqli->prepare("INSERT INTO posts (post, replyto) VALUES (?,NULL)");
	$stmt->bind_param("s", $_GET['post']);
	$stmt->execute();
	$stmt->close();
}

if(isset($_GET['reply'])){
	$stmt = $mysqli->prepare("INSERT INTO posts (post, replyto) VALUES (?,?)");
	$stmt->bind_param("ss", $_GET['reply'], $_GET['replyto']);
	$stmt->execute();
	$stmt->close();
}

buildTree($mysqli->query("SELECT * FROM posts")->fetch_all(MYSQLI_ASSOC));

function buildTree(array $filas, $replyto = NULL) {
	foreach ($filas as $fila) {
		if ($fila['replyto'] == $replyto) {
			echo "
				<details open>
					<summary>{$fila['post']}
						<details class='reply'>
							<summary>&#8617;</summary>
							<form action='?'>
								<input type='hidden' name='replyto' value='{$fila['id']}'>
								<textarea type='text' name='reply' rows='8' cols='80' ></textarea>
                                <br>
								<input type='submit' value='Responder'>
							</form>
						</details>
					</summary>
			";
			buildTree($filas, $fila['id']);
			echo "</details>";
		}
	}
}
?>