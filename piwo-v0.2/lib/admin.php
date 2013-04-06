<?php
	define ('INC_PATH', realpath(dirname(__FILE__).'/../').'/');
}

function pw_wiki_editpage ($id) {
  #out(pw_wiki_isvalidid(pw_wiki_pg($id)));

  if (pw_wiki_isns($id))
    return;

function pw_wiki_newpage ($id) {
    return false;

  global $MODE;

  $idurl = $id;
  $id = pw_s2e(utf8_rtrim(pw_url2u($id), ':').':');

  $entries  = pw_n("<input type='hidden' name='mode' value='editpage' />");
  return pw_ui_getDialogQuestion("Neue Seite erstellen", $entries, "create", "OK", "id=$idurl&mode=$MODE", "get");

}

function pw_wiki_config ($id) {
    return false;

  global $MODE;

  if (isset($_POST["config"])) {
  }

  if (pw_wiki_getcfg('debug')) {
  }

  $entries = pw_n("<input type='hidden' name='oldmode' value='$MODE' />");
  return pw_ui_getDialogQuestion("Einstellungen", $entries, "config", "OK", "id=$id&mode=$MODE");

}

function pw_wiki_savepage ($id, $data) {

  // Kontrolliere die Berechtigungen
    return false;

  $filename = pw_wiki_path($id, ST_FULL);
  $dirname = pw_wiki_path($id, ST_SHORT);

  // Kontrolliere, ob der Ordner existiert und lege ihn ggf. an
  }

  $data = pw_wiki_LE_unix($data);
  $ret = @file_put_contents($filename, $data);

  $filename = pw_s2e($filename);
  }

  return $ret;
}

function pw_wiki_rename ($id) {
  global $MODE;

  if (!isset($_SESSION["pw_wiki"]["login"]["user"])) {
  }

  $fullfilename = pw_wiki_path($id, ST_FULL);
  $fntext = pw_url2e($id);

  $isns = pw_wiki_isns($id);

  if (isset($_POST['rename'])) {

    $target = $_POST['target'];
    $targetfn = $target;

    if ($isns) {
    $targettext = pw_s2e($target);

    $typetxt = $isns ? "Der Namensraum" : "Die Seite";

    if (file_exists($targetfn)) {
    }

    if (!rename($fullfilename, $targetfn)) {
    }

    $newid = pw_wiki_path2id($targetfn);
  }

  $entries  = pw_n("<input type='hidden' name='mode' value='editpage' />");
  return pw_ui_getDialogQuestion("Umbenennen", $entries, "rename", "Umbenennen", "id=$id&mode=$MODE");

}

function pw_wiki_movepage ($id) {
  global $MODE;

  if (!isset($_SESSION["pw_wiki"]["login"]["user"])) {
  }

  $id = pw_wiki_ns($id).pw_wiki_pg($id);
  $isns = pw_wiki_isns($id);

  $fntext = pw_url2e($id);

  if (isset($_POST["move"]) || isset($_POST["overwrite"])) {

    $target = $_POST['target'];
    $targettext = pw_s2e($target);

    if (!file_exists($fullfilename)) {
    }

    if (!is_dir($targetfn)) {
      }

      if(!mkdir($targetfn, 0777, true)) {
      }

    }

    if (!isset($_POST["overwrite"])) {
      }

      #return pw_ui_getDialogInfo("Verschieben", "OVERWRITE: $id; $t", "id=$id&mode=$MODE");
    }

    if ($fullfilename == $targetfn) {
    }

    $t = "";
    if ($isns) {
      $t = pw_wiki_path($id, DNAME);
    }

    if (!rename($fullfilename, $targetfn.$t)) {
    }

    $newid = pw_s2url(pw_wiki_path2id($targetfn));

    return pw_ui_getDialogInfo("Verschieben", "Die Datei wurde nach <tt>$targettext</tt> verschoben.", "id=$newid&mode=$MODE");

  }

  $entries  = pw_n("<input type='hidden' name='mode' value='editpage' />");
    $entries .= pw_n("Den Namensraum <tt>$fntext</tt> verschieben nach...<br />");
  } else {
    $entries .= pw_n("Die Seite <tt>$fntext</tt> verschieben nach...<br />");
  $entries .= pw_n("<!--label for='id'>Ziel:</label--> <input type='text' class='textinput' autocomplete='off' name='target' id='target' value='' />");
function pw_wiki_delpage ($id) {
      $id = pw_wiki_delnamespaces($dir);
      if ($id == "") {
        $id = $oldid;
      } else {
        $outdelns = "Der Namensraum '$id' ist leer. Er wird entfernt.<hr />";

        $id = substr($id,0,strlen($id)-1);
    $id = pw_wiki_delnamespaces($dir);
    $outdelns = "";
    if ($id == "") {
      $id = $oldid;
    } else {
      $outdelns = "Der Namensraum '$id' ist leer. Er wird entfernt.<hr />";
    }


    if (pw_wiki_isns($id)) {
function pw_wiki_delnamespaces($dir) {
  if (!isset($_SESSION["pw_wiki"]["login"]["user"]))
  $dirnames = explode("/", $dir);
function pw_wiki_showcontent(/*$filename, */$id, $MODE = "") {
  //@TODO: Errorhandling!!!
  $footer = @file_get_contents(pw_wiki_path("tpl:footer", ST_FULL));

  #if (!file_exists($filename) or !is_file($filename)) {
  $data = @file_get_contents($filename);

  $data = pw_wiki_LE_unix($data);
  $data = $header."\n".$data."\n".$footer;

  if (! utf8_check($data)) {
  }

  $hdlen = count(explode("\n", $header));
  $ftlen = count(explode("\n", $footer));

  $out = lexerconf($data, $hdlen, $ftlen);

  return $out;
}


$user = "root";
$pwd = "qwertz";

function pw_wiki_login($id) {
  global $MODE;

  if (isset($_POST["login"])) {
    $pass = $_POST["password"];

    if ($user == $login and $pass == $pwd) {
  }

  if (isset($_POST["logout"])) {
  }

  if (isset($_SESSION["pw_wiki"]['login']["user"])) {
  }

  $entries = "<label for='username'>Benutzer: </label><input type='text' class='textinput' name='username' /><br />";
}

function pw_wiki_getfilelist($id = null) {
  #var_dump(pw_wiki_getcfg());

  $ns = pw_wiki_ns($id);
  $path = pw_wiki_path($ns, ST_NOEXT);

  $strout = "";
  }

  $p = "../".rtrim($path, "/")."/";
  $data = glob ($p."*");

  #var_dump($data);

  if (!$data) {
  }

  foreach ($data as $k => $i) {
    $i = pw_u2t($i);

    if (is_dir($i)) {
    }

  }

  if ($dirs) sort($dirs);
  if ($files) sort($files);

  $out = array_merge($dirs, $files);

  return $out;

}

function pw_wiki_showpages($id = null) {
  #  return false;

  #out($id);
  $path = pw_wiki_path($ns, ST_NOEXT);

  $strout = "";
  $data = glob ("$path/*");

  #out($path);
  #out($data);

  // Leeres Verz. gefunden... Löschen!
      #pw_debug($i, "ANFANG");

      $i = pw_s2u($i);
      #pw_debug(utf8_strtolower($i));

      if ($i != utf8_strtolower($i)) {
      $i = utf8_strtolower($i);
      $i = pw_u2t($i);

      if (is_dir($i)) {
      }

    }
  }

  if ($dirs) sort($dirs);
  if ($files) sort($files);

  $out = array_merge($dirs, $files);
  foreach ($out as $k => $i) {

    $strout .= "<tr style='background: black'>";
    $strout .= "<td>";

    if ($i['TYPE'] == "TEXT") {
  }

  $strout .= "</table>";
}

function rrmdir($dir) {
 }

?>