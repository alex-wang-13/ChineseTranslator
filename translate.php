<head>
  <title>Translate!</title>
  <!-- The following script is deprecated. It may be brought back in a future version -->
  <!--script>
    function highlightWords() {
      // getting the text input box
      var input = document.getElementById("textbox");
      var text = input.value;
      // a variable to hold the new generated html
      var highlightedText = "";

      // iterate through the words
      for (var i = 0; i < text.length; i++) {
        // use \u4E00-\u9FFF for normal chinese character ranges
        // use \u3400-\u4DBF for rarer chinese characters

        // appending text + highlighting chinese text
        if (text.charAt(i).match(/[\u4E00-\u9FFF]/)) {
          highlightedText += "<span style='background-color: yellow'>" + text.charAt(i) + "</span>";
        } else {
          highlightedText += "" + text.charAt(i);
        }
      }

      // updating html
      document.getElementById("highlighted-text").innerHTML = highlightedText;
    }
  </script-->
</head>
<body>
  <h3>Give some Chinese text:</h3>
  <hr>
  <!-- A textbox for user input -->
  <span>
    <div>
      <label for="textbox">Enter text:</label>
      <br>
      <form method="POST">
        <textarea name="text" id="textbox" rows="5" cols="50"></textarea>
        <br>
        <input type="submit" name="translate" value="Translate">
      </form>
    </div>
    <!-- A script to translate the words in the text box with user-provided definitions -->
    <div>
      <p>Translated Text:</p>
      <?php
        session_start();
        // verify that the dictionary and _POST variables are initialized
        if(isset($_SESSION["dictionary"]) && isset($_POST["translate"])) {
          // grab the global dictionary variable
          $dictionary = $_SESSION["dictionary"];
          // grab the text from _POST
          $text = $_POST["text"];
          // split the text into an array of graphemes, i.e. language units
          // since utf-8 characters have variable lengths, split by //s to get all graphenes
          foreach(preg_split('//u', $text, null, PREG_SPLIT_NO_EMPTY) as $grapheme) {
            // if the grapheme is in the dictionary, then replace it with its translation
            if(isset($dictionary[$grapheme])) {
              $text = mb_ereg_replace($grapheme, "[".$dictionary[$grapheme]."]", $text);
            }
          }
        }
        // prints the translation
        print_r($text);
      ?>
    </div>
  </span>
  <hr>
  <!-- A form to enter Chinese text and its English translations as key-value pairs -->
  <form method="POST">
    <label for="key">Enter Chinese:</label>
    <input type="text" name="key" id="key">
    <br><br>
    <label for="value">Enter English:</label>
    <input type="text" name="value" id="value">
    <br><br>
    <!-- Add the translation to the dictionary -->
    <input type="submit" name="submit" value="Store in Dictionary">
    <!-- Clear the dictionary -->
    <input type="submit" name="reset" value="Reset Dictionary">
    <!-- Show the dictionary on the webpage -->
    <input type="submit" name="show" value="Show Dictionary">
  </form>
  <!-- A script to validate incoming dictionary entries -->
  <?php
    session_start();

    // initialize the dictionary
    if(!isset($_SESSION["dictionary"])) {
      $_SESSION["dictionary"] = array();
    }

    // if _POST["submit"] is initialized, validate the entries
    if(isset($_POST["submit"])) {
      // get the key and value
      $key = $_POST["key"];
      $value = $_POST["value"];
      // validate the key and value; the key should only have chinese characters and the value only non-chinese
      if(preg_match("/^[\x{4e00}-\x{9fff}]+$/u", $key) && !preg_match("/^[\x{4e00}-\x{9fff}]+$/u", $value)) {
        $dictionary = $_SESSION["dictionary"];
        $dictionary[$key] = $value;
        // update the dictionary
        $_SESSION["dictionary"] = $dictionary;
      }
    }

    // if _POST["reset"] then reinitialize the dictionary
    if(isset($_POST["reset"])) {
      $_SESSION["dictionary"] = array();
      echo "Dictionary: <br>";
      print_r($_SESSION["dictionary"]);
    }

    // if _POST["show"] then show the surrent dictionary
    if(isset($_POST["show"])) {
      echo "Dictionary: <br>";
      print_r($_SESSION["dictionary"]);
    }
  ?>
  <br><br>
  <hr>
  <p>By: Alex Wang</p>
</body>
