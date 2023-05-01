<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  <title>Translate!</title>
</head>

<body>
  <div class="container mt-4">
    <h3>Give some Chinese text:</h3>
    <!-- A textbox for user input -->
    <div class="mb-4">
      <form method="POST">
        <div class="form-group">
          <label for="textbox">Enter text:</label>
          <textarea name="text" id="textbox" class="form-control" rows="5" cols="50"></textarea>
        </div>
        <button type="submit" name="translate" class="btn btn-primary">Translate</button>
      </form>
    </div>
    <!-- A form to enter Chinese text and its English translations as key-value pairs -->
    <div class="mb-4">
      <form method="POST">
        <div class="form-group">
          <label for="key">Enter Chinese Character:</label>
          <input type="text" name="key" id="key" class="form-control">
        </div>
        <div class="form-group">
          <label for="value">Enter Translation:</label>
          <input type="text" name="value" id="value" class="form-control">
        </div>
        <!-- Add the translation to the dictionary -->
        <button type="submit" name="submit" class="btn btn-success">Store in Dictionary</button>
        <!-- Clear the dictionary -->
        <button type="submit" name="reset" class="btn btn-danger">Reset Dictionary</button>
      </form>
    </div>
    <!-- A script to validate incoming dictionary entries -->
    <?php
      session_start();

      // initialize the dictionary
      if (!isset($_SESSION["dictionary"])) {
        $_SESSION["dictionary"] = array();
      }

      // initailize input sentence
      if (!isset($_SESSION["input"])) {
        $_SESSION["input"] = "Enter a sentence in Chinese.";
      }

      // if _POST["submit"] is initialized, validate the entries
      if (isset($_POST["submit"])) {
        // get the key and value
        $key = $_POST["key"];
        $value = $_POST["value"];
        // validate the key and value; the key should only have chinese characters and the value only non-chinese
        if (preg_match("/^[\x{4e00}-\x{9fff}]$/u", $key) && !preg_match("/^[\x{4e00}-\x{9fff}]+$/u", $value)) {
          $dictionary = $_SESSION["dictionary"];
          $dictionary[$key] = $value;
          // update the dictionary
          $_SESSION["dictionary"] = $dictionary;
        } else {
          echo "INVALID ENTRY / NOTHING STORED. <br>";
        }
      }

      // if _POST["reset"] then reinitialize the dictionary
      if (isset($_POST["reset"])) {
        $_SESSION["dictionary"] = array();
      }

      // show the dictionary
      echo "<hr><h4>Dictionary:</h4>";
      echo "<pre>";
      print_r($_SESSION["dictionary"]);
      echo "</pre>";
      echo "<hr><h4>Translated Text:</h4>";

      // verify that the dictionary and _POST variables are initialized
      if (isset($_SESSION["dictionary"])) { //&& isset($_POST["translate"])) {
        // grab the global dictionary variable
        $dictionary = $_SESSION["dictionary"];
        // grab the text from _POST or _SESSION
        $text = $_SESSION["input"];
        if (isset($_POST["translate"])) {
          $text = $_POST["text"];
          $_SESSION["input"] = $text;
        }
        // split the text into an array of graphemes, i.e. language units
        // since utf-8 characters have variable lengths, split by //s to get all graphenes
        foreach (preg_split('//u', $text, null, PREG_SPLIT_NO_EMPTY) as $grapheme) {
          // if the grapheme is in the dictionary, then replace it with its translation
          if (isset($dictionary[$grapheme])) {
            $text = mb_ereg_replace($grapheme, "[" . $dictionary[$grapheme] . "]", $text);
          }
        }
      }
      // prints the translation
      print_r($text);
      echo "<hr>";
    ?>
    </div>
  </div>
  <footer
    class="bg-dark text-center text-lg-start"
    style="background-color: #212121; color: white"
  >
    <div class="container p-4">
      <div class="col-sm-4 col-12">
        <h5 class="text-uppercase">Contact</h5>
        <ul class="list-unstyled">
          <li>
            <a href="mailto:alex.wang4@case.edu" target="_blank">Email</a>
          </li>
          <li>
            <a href="https://github.com/alex-wang-13" target="_blank"
              >Github</a
            >
          </li>
          <li>
            <a
              href="https://www.linkedin.com/in/alex-wang-0525b4217/"
              target="_blank"
              >LinkedIn</a
            >
          </li>
        </ul>
      </div>
      &copy; 2023 Alex Wang. All rights reserved.
    </div>
  </footer>
</body>
