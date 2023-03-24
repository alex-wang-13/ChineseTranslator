<head>
	<title>Highlight Textbox Words</title>
	<script>
		function highlightWords() {
			var input = document.getElementById("textbox");
			var text = input.value;
			var words = text.split(" ");
			var highlightedText = "";

			for (var i = 0; i < words.length; i++) {
                // use \u4E00-\u9FFF for normal chinese character ranges
                // use \u3400-\u4DBF for rarer chinese characters
				// use \u2B740-\u2B81F for more uncommon chinese characters
				// use 20000-2A6DF, 2A700–2B73F, 2B820–2CEAF, 2CEB0–2EBEF, 30000–3134F, 31350–323AF
				if (words[i].match(/[\u4E00-\u9FFF]/)) {
					highlightedText += "<span style='background-color: green'>" + words[i] + "</span> ";
				} else if (words[i].match(/[\u3400-\u4DBF]/)) {
					highlightedText += "<span style='background-color: yellow'>" + words[i] + "</span> ";
				} else if (words[i].match(/[\u2B740-\u2B81F]/)) {
					highlightedText += "<span style='background-color: orange'>" + words[i] + "</span> ";
				} else {
					highlightedText += words[i] + " ";
				}
			}

			document.getElementById("highlighted-text").innerHTML = highlightedText;
		}
	</script>
</head>
<body>
	<label for="textbox">Enter text:</label>
	<br>
	<?php
		if(isset($_POST['submit'])){
			$text = $_POST['text'];
			echo '<textarea id="textbox" oninput="highlightWords()" rows="5" cols="50">'.$text.'</textarea>';
		} else {
			echo '<textarea id="textbox" oninput="highlightWords()" rows="5" cols="50"></textarea>';
		}
	?>
	<br>
	<input type="submit" name="submit" value="Submit">
	<br>
	<p>Highlighted Text:</p>
	<p id="highlighted-text"></p>
    <hr>
    <p>Note: further development can include inputting website URLs and returning an html page and translating Chinese text to English instead of just highlighting it.</p>
</body>
