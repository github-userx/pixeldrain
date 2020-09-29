# pixeldrain
Simple Pixeldrain.com downloader written in PHP

# Features
 - Can download single files or lists
 - Downloads with Curl and gives progress status
 - What more do you want?

# Dependencies
The curl extension for PHP.

# Usage
  php pixeldrain.php [ids]
  It will download files in the current directory, you can change this by editing the $cfg setting in pixeldrain.php
  
# Limitations
If a file has been downloaded too much through the API it will require a Captcha to be filled out, this is not supported. See https://pixeldrain.com/api for more information.
 
# Todo
 - Better command-line argument handling
 - Ability to set download path in a argument
 - Maybe uploading support in the future

