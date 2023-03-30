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
          highlightedText += " " + words[i] + " ";
        }
      }

      // updating html
      document.getElementById("highlighted-text").innerHTML = highlightedText;
    }
  </script>
</head>
<body>
  <br>
  <!-- php to update the textarea with the highlighted text -->
  <span>
    <div>
      <label for="textbox">Enter text:</label>
      <br>
      <textarea id="textbox" oninput="highlightWords()" rows="5" cols="50"></textarea>
    </div>
    <div>
      <p>Highlighted Text:</p>
      <p id="highlighted-text"></p>
    </div>
  </span>
  <form method="POST">
    <label for="key">Enter Chinese:</label>
    <input type="text" name="key" id="key">
    <br><br>
    <label for="value">Enter English:</label>
    <input type="text" name="value" id="value">
    <br><br>
    <input type="submit" name="submit" value="Store in Dictionary">
    <input type="submit" name="reset" id="reset" value="Reset Dictionary">
  </form>
  <?php
    // start a session so that the dictionary can be updated between each submit
    session_start();

    // initialize the dictionary
    if(!isset($_SESSION["dictionary"])) {
      $_SESSION["dictionary"] = array();
    }

    if(isset($_POST["submit"])) {
      // key the key and value
      $key = $_POST["key"];
      $value = $_POST["value"];
      // validate the key and value; the key should only have chinese characters and the value only non-chinese
      if(preg_match("/^[\x{4e00}-\x{9fff}]+$/u", $key) && !preg_match("/^[\x{4e00}-\x{9fff}]+$/u", $value)) {
        $dictionary = $_SESSION["dictionary"];
        $dictionary[$key] = $value;
        // update the dictionary
        $_SESSION["dictionary"] = $dictionary;
        echo "Chinese: ".$key."<br>";
        echo "English: ".$value."<br>";
      } else {
        echo "Chinese: [invalid] <br>";
        echo "English: [invalid] <br>";
      }
      echo "Dictionary: <br>";
      print_r($_SESSION["dictionary"]);
    }

    if(isset($_POST["reset"])) {
      $_SESSION["dictionary"] = array();
      echo "Dictionary: <br>";
      print_r($_SESSION["dictionary"]);
    }
  ?>
  <hr>
  <p>Note: further development can include inputting multiple website URLs and translating Chinese text (or other langauges) to English instead of just highlighting it.</p>
</body>
