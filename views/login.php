<?php
//
// ZoneMinder web login view file, $Date: 2008-09-26 10:47:20 +0100 (Fri, 26 Sep 2008) $, $Revision: 2632 $
// Copyright (C) 2001-2008 Philip Coombes
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
//

xhtmlHeaders(__FILE__, $SLANG['Login'] );
?>
<body class="zm">
  <?php require_once("header.php"); ?>
  
  <div class="view-wrapper"> <!-- begin view-wrapper -->

    <div class="container">
    <form name="loginForm" id="loginForm" method="post" class="form-signin" action="<?= $_SERVER['PHP_SELF'] ?>">
        <input type="hidden" name="action" value="login"/>
        <input type="hidden" name="view" value="postlogin"/>
        <h2 class="form-signin-heading">Please sign in</h2>
        <input name="username" type="text" class="form-control" placeholder="<?= $SLANG['Username'] ?>" autofocus="">
        <input name="password" type="password" class="form-control" placeholder="<?= $SLANG['Password'] ?>">
        <button name="login" class="btn btn-lg btn-primary btn-block" type="submit">
          <span class="glyphicon glyphicon-log-in"></span> 
          <?= $SLANG['Login'] ?>
        </button>
      </form>
    </div>
  </div> <!-- end view-wrapper -->

  <?php require_once("footer.php"); ?>