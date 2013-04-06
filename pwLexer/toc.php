<?php
      foreach($indextable['CONT'] as $idx) {
function plugin_nstoc($lexer, $node) {
function createindextable($lexer, $node = NULL) {

  if (!is_object($lexer) or $lexer->checkNode($node) === false) {
    throw new Exception("CreateIndexTable: Wrong Parametertype(s).");
    return;
  }



  for ($node = $lexer->firstChild($node); $node != null; $node = $lexer->nextSibling($node)) {
    #out($node);

    if ($node['NAME'] == "header") {
      $fc = $lexer->getText($node);

      $entry['TEXT'] = utf8_trim(pw_e2u($fc));
      $entry['LEVEL'] = utf8_strlen($node['CONFIG'][0]);

      if ($GLOBALS['indextable']['LEVELS']['LASTLEVEL'] > $entry['LEVEL']) {
        $GLOBALS['indextable']['LEVELS'][$entry['LEVEL']+1] = 0;
        $GLOBALS['indextable']['LEVELS'][$entry['LEVEL']+2] = 0;
        $GLOBALS['indextable']['LEVELS'][$entry['LEVEL']+3] = 0;
        $GLOBALS['indextable']['LEVELS'][$entry['LEVEL']+4] = 0;
      }

      $GLOBALS['indextable']['LEVELS'][$entry['LEVEL']]++;

      $l = $GLOBALS['indextable']['LEVELS'];
      $l = strrtrim("$l[1].$l[2].$l[3].$l[4].$l[5]", ".0");
      $entry['ID'] = $l;
      $GLOBALS['indextable']['CONT'][] = $entry;

      $GLOBALS['indextable']['LEVELS']['LASTLEVEL'] = $entry['LEVEL'];
    }

    if ($lexer->hasChildNodes($node) and $node['NAME'] != "notoc") {
      createindextable($lexer, $node);
    }

  }

}

function getindextable() {
  return $GLOBALS['indextable'];
}

function getindexitem($idxortxt, $id = true) {
  global $indextable;
  $idxortxt = strtolower(pw_s2u(trim($idxortxt)));
  if (!is_array($indextable['CONT'])) {
    return false;
  }

  foreach ($indextable['CONT'] as $item) {
    if ($id and $item['ID'] == $idxortxt) {
      return $item;
    }
    $itemtext = strtolower(pw_s2e(trim($item['TEXT'])));
    #out("$itemtext --- $idxortxt");
    if (!$id and $itemtext == $idxortxt) {
      #out($item);
      return $item;
    }
  }

  return NULL;
}

function plugin_trace ($lexer, $node, $sep = "&raquo;") {
  $sep = ' '.$sep.' ';

  $id = pw_wiki_getcfg('id');
  $mode = pw_wiki_getcfg('mode');
  $startpage = pw_wiki_getcfg('startpage');

  #out($id);

  $o = "<a href='?mode=cleared&id=".pw_s2url($startpage)."'>Home</a>";

  $fullpath = explode(":", $id);
  $pg = array_pop($fullpath);

  $p = "";
  foreach ($fullpath as $i) {
    $p .= "$i:";
    $i = pw_url2u($i);
    $i = utf8_ucfirst($i);
    $i = pw_s2e($i);
    $o .= $sep."<a href='?mode=cleared&id=".rtrim($p, ":")."'>".$i."</a>";
  }

  $pg = pw_url2u($pg);
  $pg = utf8_ucfirst($pg);
  $pg = pw_s2e($pg);
  $o .= $sep.$pg;

  return $o;
}

?>