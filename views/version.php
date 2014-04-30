<?php
	if (ZM_DYN_DB_VERSION && (ZM_DYN_DB_VERSION != ZM_VERSION)) {
		die("mismatch");
	}
	elseif (verNum(ZM_DYN_LAST_VERSION) <= verNum(ZM_VERSION)) {
		die("noupdate");
	}
	else {
		die("update");
	}
?>