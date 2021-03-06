<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

function get_nonexisting_file($folder, $extension, $create){
	/*
	Generates unique filename that does not exist yet
	:param folder: folder in which generate the filename
	:param extension: extension of the generated filename, can be blank
	:param create: if set to true, the new blank file is created
	:return: absolute file path
	*/
	$folder = realpath($folder);
	$i = 0;
	while (true){
		$hex = dechex($i);
		$filepath = ("{$folder}/{$hex}{$extension}");
		if (!file_exists($filepath)){
			if ($create)
				file_put_contents($filepath, "");
			return $filepath;
		}
		$i++;
	}
}

$target_file = get_nonexisting_file("/tmp/", "", false);
move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

header("Content-Type: text/plain");

$language = escapeshellarg(isset($_POST["lang"]) ? $_POST["lang"] : 'eng');
print(shell_exec("tesseract {$target_file} stdout -l {$language}"));

unlink($target_file);

die();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>OCR Online</title>
		<meta charset="UTF-8">
		<style>
			@import url('https://fonts.googleapis.com/css?family=Lato');
			* {
				box-sizing: border-box;
				font-family: 'Lato', sans-serif;
			}

			body {
				height: 100vh;
				overflow-y: hidden;
				display: flex;
				justify-content: center;
				align-items: center;
				background: #f5f5f5;
			}

			.container {
				background: #fafafa;
				text-align: center;
				padding: 24px;
				box-shadow: 0px 3px 5px rgba(0, 0, 0, .35);
			}
			
			input, select {
				margin: 10px 0;
				text-align: center;
			}

			#p_select_img {
				float: left;
				display: inline;
				margin: 0;
			}

			#image {
				margin-bottom: 16px;
			}

			.language-container {
				display: flex;
				justify-content: space-between;
			}

			input[type=submit] {
				padding: 12px 6px;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<h1 id="title">Free online OCR</h1>
			<form method="post" enctype="multipart/form-data">
				<p id="p_select_img">Select image to upload: </p>
				<br>
				<input type="file" name="image" id="image"><br>
				<div class="language-container">
					<p id="p_language">Language:</p>
					<select id="select_lang" onchange="reloadLang()" name="lang">
						<option value="eng">ENG</option>
						<option value="ces">CZ</option>
						<option value="deu">DE</option>
						<option value="rus">RU</option>
					</select>
				</div>
				
				<input id="btn_submit" type="submit" value="Upload Image" name="submit">
			</form>
		</div>
		<script>
		let title = document.getElementById("title");
		let p_select_img = document.getElementById("p_select_img");
		let p_language = document.getElementById("p_language");
		let btn_submit = document.getElementById("btn_submit");
		let select_lang = document.getElementById("select_lang");

		function reloadLang(){
			switch(select_lang.value){
				case "rus":
					title.innerHTML = "Онлайн распознавание текста";
					p_language.innerHTML = "Язык: ";
					p_select_img.innerHTML = "Выберите файл картинки: ";
					btn_submit.value = "Выполнять распознавание текста";
					break;
				case "deu":
					title.innerHTML = "Online Texterkennung";
					p_language.innerHTML = "Sprache: ";
					p_select_img.innerHTML = "Bild auswählen: ";
					btn_submit.value = "Texterkennung starten";
					break;
				case "ces":
					title.innerHTML = "Online rozpoznávání textu";
					p_language.innerHTML = "Jazyk: ";
					p_select_img.innerHTML = "Vyberte soubor obrázku: ";
					btn_submit.value = "Provést rozpoznání textu";
					break;
				default:
					title.innerHTML = "Free online OCR";
					p_language.innerHTML = "Language: ";
					p_select_img.innerHTML = "Select image to upload: ";
					btn_submit.value = "Do the OCR";
					break;
			}
		}

		switch(navigator.language || navigator.userLanguage){
			case "ru":
				select_lang.value = "rus";
				break;
			case "de":
				select_lang.value = "deu";
				break;
			case "cs":
				select_lang.value = "ces";
				break;
			default:
				select_lang.value = "eng";
				break;
		}
		reloadLang();
		</script>
	</body>
</html>
