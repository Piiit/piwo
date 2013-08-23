<?php
function pw_ui_printDialogWrap($dialog=-1) {
	if ($dialog == -1) {
		StringTools::htmlIndentPrint ("</div>", END);
		return;
	}
	if ($dialog == "") {
		return false;
	}

	StringTools::htmlIndentPrint ("<!-- MODALDIALOG START -->");
	StringTools::htmlIndentPrint ('<div id="modal"><div class="overlay-decorator"></div><div class="overlay-wrap"><div class="overlay"><div class="dialog-wrap"><div class="dialog" id="dialog"><div class="dialog-decorator"><div id="ajax_dialog">', START);
	StringTools::htmlIndentPrint ($dialog);
	StringTools::htmlIndentPrint ('</div></div></div></div></div></div></div>', END);
	StringTools::htmlIndentPrint ("<div style='position: absolute; top: 0; left: 0; width: 100%; display: block; height: 100%; overflow: hidden'>");
	StringTools::htmlIndentPrint ("<!-- MODALDIALOG END -->", START);

	return true;
}

function pw_ui_getDialogInfo($title, $desc, $href, $method = "post") {
	$o  = StringTools::htmlIndent();
	$o .= StringTools::htmlIndent("<form method='$method' accept-charset='utf-8' id='form'>", START);
	$o .= StringTools::htmlIndent("<h1>$title</h1>");
	$o .= StringTools::htmlIndent("<div>$desc</div>");
	$o .= StringTools::htmlIndent("<div>", START);
	$o .= StringTools::htmlIndent("<a href='?$href' id='submit'>OK</a>");
	$o .= StringTools::htmlIndent("</div>", END);
	$o .= StringTools::htmlIndent("</form>", END);
	return $o;
}

function pw_ui_getDialogQuestion($title, $desc, $byesname, $byestext, $bno, $method = "post") {
	$o  = StringTools::htmlIndent();
	$o .= StringTools::htmlIndent("<form method='$method' accept-charset='utf-8' id='form'>", START);
	$o .= StringTools::htmlIndent("<h1>$title</h1>");
	$o .= StringTools::htmlIndent("<div>", START);
	$o .= $desc;
	$o .= StringTools::htmlIndent("</div>", END);
	$o .= StringTools::htmlIndent("<div>", START);
	$o .= StringTools::htmlIndent("<input id='submit' type='submit' name='$byesname' value='$byestext' />");
	$o .= StringTools::htmlIndent("<a href='?$bno'>Abbrechen</a>");
	$o .= StringTools::htmlIndent("</div>", END);
	$o .= StringTools::htmlIndent("</form>", END);
	return $o;
}

function pw_ui_getButton($name, $href, $shortcut = null) {
	$o  = StringTools::htmlIndent("<span class='edit'>");
	$o .= StringTools::htmlIndent("<a href='?$href'>");
	if ($shortcut !== null) {
		$o .= StringTools::htmlIndent("<span class='shortcut'>$shortcut</span>");
	}
	$o .= StringTools::htmlIndent("$name</a></span>");

	return $o;
}

?>