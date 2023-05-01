<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  <title>Translate!</title>
</head>

<body class="bg-dark text-light">
  <div class="container mt-4">
    <h3>Test your Chinese...</h3>
    <!-- A textbox for user input -->
    <div class="mb-4">
      <form method="POST">
        <div class="form-group">
          <label for="textbox">Enter text manually or automatically generate:</label>
          <br/>
          <?php
            session_start();

            // code to generate chinese text
            if (isset($_POST['generate'])) {
              // retrieve the api key for chat-gpt
              $key = trim(file_get_contents('../../../api-key.txt'));

              // define prompt
              $style = 'neutral';
              if (isset($_POST['prompt-style'])) {
                $style = $_POST['prompt-style'];
              }
              $prompt = 'Give an example paragraph in Chinese in a ' . $style . ' tone. Include no English in your response.';

              // shell command to access chat-gpt
              $cmd = 'curl https://api.openai.com/v1/chat/completions -H "Content-Type: application/json" -H "Authorization: Bearer ' . $key . '" -d \'{ "model": "gpt-3.5-turbo", "messages": [{"role": "user", "content": "' . $prompt . '"}], "temperature": 0.7 }\'';

              // get the response from the api
              $response = shell_exec($cmd);

              // decode the json response from api + extract chat-gpt's output
              $arr = json_decode($response, true, 512, 0);
              $content = $arr['choices']['0']['message']['content'];

              // display populated textarea
              echo '<textarea name="text" id="textbox" class="form-control" rows="5" cols="50">' . $content . '</textarea>';
              $_SESSION['input'] = $content;
            } else {
              // use previously input to populate textarea
              echo '<textarea name="text" id="textbox" class="form-control" rows="5" cols="50">' . $_SESSION['input'] . '</textarea>';
            }
          ?>
        </div>
        <br/>
        <button type="submit" name="translate" class="btn btn-primary">Translate</button>
        <br/>
        <br/>
        <p>Choose the style of text you'd like to translate:</p>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="prompt-style" value="conversational">
          <label class="form-check-label" for="conversational">Conversational Prompt</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="prompt-style" value="journalistic">
          <label class="form-check-label" for="journalistic">Journalistic Prompt</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="prompt-style" value="academic">
          <label class="form-check-label" for="academic">Academic Prompt</label>
        </div>
        <br/>
        <button type="submit" name="generate" class="btn text-light" style="background-color: orangered;">Generate Chinese</button>
      </form>
    </div>
    <hr/>
    <!-- A form to enter Chinese text and its English translations as key-value pairs -->
    <h3>Add words to the dictionary:</h3>
    <div class="mb-4">
      <form method="POST">
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label for="key">Enter Chinese Character:</label>
              <input type="text" name="key" id="key" class="form-control">
            </div>
          </div>
          <div class="col-6">
            <div class="form-group">
              <label for="value">Enter Translation:</label>
              <input type="text" name="value" id="value" class="form-control">
            </div>
          </div>
        </div>
        <br/>
        <!-- Add the translation to the dictionary -->
        <button type="submit" name="submit" class="btn btn-success">Store in Dictionary</button>
        <!-- Clear the dictionary -->
        <button type="submit" name="reset" class="btn text-light" style="background-color: firebrick;">Reset Dictionary</button>
      </form>
    </div>
    <!-- A script to validate incoming dictionary entries -->
    <?php
      session_start();

      // initialize the dictionary
      if (!isset($_SESSION['dictionary'])) {
        $_SESSION['dictionary'] = array();
      }

      // initailize input sentence
      if (!isset($_SESSION['input'])) {
        $_SESSION['input'] = '';
      }

      // if _POST["submit"] is initialized, validate the entries
      if (isset($_POST['submit'])) {
        // get the key and value
        $key = $_POST['key'];
        $value = $_POST['value'];
        // validate the key and value; the key should only have chinese characters and the value only non-chinese
        if (preg_match('/^[\x{4e00}-\x{9fff}]$/u', $key) && !preg_match('/^[\x{4e00}-\x{9fff}]+$/u', $value)) {
          $dictionary = $_SESSION['dictionary'];
          $dictionary[$key] = trim(strtolower($value));
          // update the dictionary
          $_SESSION['dictionary'] = $dictionary;
        } else {
          echo '<p class="text-light">INVALID ENTRY / NOTHING STORED.</p>';
        }
      }

      // if _POST["reset"] then reinitialize the dictionary
      if (isset($_POST['reset'])) {
        $_SESSION['dictionary'] = array();
      }

      // show the dictionary
      echo '<h4>Dictionary:</h4>';
      echo '<pre>';
      print_r($_SESSION['dictionary']);
      echo '</pre>';
      echo '<hr/><h3>See the translated text:</h3><br/>';

      // verify that the dictionary and _POST variables are initialized
      if (isset($_SESSION['dictionary'])) { //&& isset($_POST['translate'])) {
        // grab the global dictionary variable
        $dictionary = $_SESSION['dictionary'];
        // grab the text from _POST or _SESSION
        $text = $_SESSION['input'];
        if (isset($_POST['translate'])) {
          $text = $_POST['text'];
          $_SESSION['input'] = $text;
        }
        // split the text into an array of graphemes, i.e. language units
        // since utf-8 characters have variable lengths, split by //s to get all graphenes
        foreach (preg_split('//u', $text, null, PREG_SPLIT_NO_EMPTY) as $grapheme) {
          // if the grapheme is in the dictionary, then replace it with its translation
          if (isset($dictionary[$grapheme])) {
            $text = mb_ereg_replace($grapheme, '<span style="background-color: green;">[' . $dictionary[$grapheme] . ']</span>', $text);
          }
        }
      }
      // prints the translation
      print_r($text);
      echo '<hr/>';
    ?>
    </div>
  </div>
  <footer
    class="text-center text-lg-start text-light"
    style="background-color: black;"
  >
    <div class="container p-4">
      <div class="row">
        <div class="col-sm-6 col-6">
          <h5 class="text-uppercase">Contact</h5>
          <ul class="list-unstyled">
            <li>
              <a href="mailto:alex.wang4@case.edu" target="_blank" style="color: white;">Email</a>
            </li>
            <li>
              <a href="https://github.com/alex-wang-13" target="_blank" style="color: white;"
                >Github</a
              >
            </li>
            <li>
              <a
                href="https://www.linkedin.com/in/alex-wang-0525b4217/"
                target="_blank" style="color: white;"
                >LinkedIn</a
              >
            </li>
          </ul>
        </div>
        <div class="col-sm-6 col-6">
          <h5 class="text-uppercase">More Information</h5>
          <ul class="list-unstyled">
            <li>
              <a class="text-uppercase" href="/README.md" target="_blank" style="color: white;">Readme</a>
            </li>
            <li>
              <a href="https://github.com/alex-wang-13/ChineseTranslator" target="_blank" style="color: white;"
                >Source Code</a
              >
            </li>
          </ul>
        </div>
        &copy; 2023 Alex Wang. All rights reserved.
      </div>
    </div>
  </footer>
</body>
