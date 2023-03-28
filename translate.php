<head>
  <title>Highlight Textbox Words</title>
  <script>
    function highlightWords() {
      // getting the text input box
      var input = document.getElementById("textbox");
      var text = input.value;
      // splitting the text values into separate words
      var words = text.split(/[\s\.]/);
      // a variable to hold the new generated html
      var highlightedText = "";

      // iterate through the words
      for (var i = 0; i < words.length; i++) {
        // use \u4E00-\u9FFF for normal chinese character ranges
        // use \u3400-\u4DBF for rarer chinese characters

        // appending text + highlighting chinese text
        if (words[i].match(/[\u4E00-\u9FFF]/)) {
          highlightedText += "<span style='background-color: yellow'>" + words[i] + "</span>";
        } else {
          highlightedText += words[i] + " ";
        }
      }

      // updating html
      document.getElementById("highlighted-text").innerHTML = highlightedText;
    }
  </script>
</head>
<body>
  <label for="textbox">Enter text:</label>
  <br>
  <!-- php to update the textarea with the highlighted text -->
  <?php
    if(isset($_POST['submit'])) {
      $text = $_POST['text'];
      // add highlighted text
      echo '<textarea id="textbox" rows="5" cols="50">'.$text.'</textarea>';
    } else {
      // create the textbox
      echo '<textarea id="textbox" oninput="highlightWords()" rows="5" cols="50"></textarea>';
    }

  ?>
  <br>
  <input type="submit" name="submit" value="Submit">
  <br>
  <p>Highlighted Text:</p>
  <p id="highlighted-text"></p>
    <hr>
    <p>Note: further development can include inputting multiple website URLs and translating Chinese text (or other langauges) to English instead of just highlighting it.</p>
</body>
