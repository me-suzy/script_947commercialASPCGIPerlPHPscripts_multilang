<?php

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2001 by Francisco Burzi (fbc@mandrakesoft.com)         */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if ($radminsuper==1) {
    adminmenu("admin.php?op=hreferer", "HTTP Verknüpfungen", "referer.gif");
}
/* This has to to rewritten for vbPortal once the config is finalized 
if ($radminsuper==1) {
    adminmenu("admin.php?op=Configure", "Vorlieben", "preferences.gif");
}
*/
?>
