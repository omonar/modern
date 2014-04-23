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

if(isset($_SESSION['user'])) {
  if(!isset($_POST['view'])) {
    $_POST['view'] = "playback";
  }
  $response = dbFetchOne("SELECT * FROM Users WHERE Id={$_SESSION['user']['Id']} LIMIT 1");

  if(!$response) {
    $error = "ERROR: Failed to fully autheticate user, please contact your system administrator!";
  }
  // potentially insecure code. needs to actually recheck credentials to stop injection.
  elseif(isset($_SESSION['user']['Username'])) {
    header("Location: ?view={$_POST['view']}");
  }
}

xhtmlHeaders(__FILE__, $SLANG['Login'] );
?>
<body class="zm">
  
  <div class="view-wrapper"> <!-- begin view-wrapper -->

    <div class="container">
    <form name="loginForm" id="loginForm" method="post" class="form-signin" action="<?= $_SERVER['PHP_SELF'] ?>">
        <input type="hidden" name="action" value="login"/>
        <?php
          if(isset($_GET['view']) && ctype_alpha($_GET['view'])) {
            echo "<input type=\"hidden\" name=\"view\" value=\"{$_GET['view']}\">";
          }
          else {
            echo "<input type=\"hidden\" name=\"view\" value=\"playback\">";
          }
        ?>
        <h2 class="form-signin-heading">Please sign in</h2>
        <input name="username" type="text" class="form-control" placeholder="<?= $SLANG['Username'] ?>" autofocus="">
        <input name="password" type="password" class="form-control" placeholder="<?= $SLANG['Password'] ?>">
        <?php
          if(!isset($_SESSION['user']) && isset($_SESSION['username'])) {
        ?>
            <div class="alert alert-danger">
              <p>Incorrect username and / or password...</p>
            </div>
        <?php
          }
        ?>
        <button name="login" class="btn btn-lg btn-primary btn-block" type="submit" value="Login">
          <span class="fa fa-sign-in"></span> 
          <?= $SLANG['Login'] ?>
        </button>
      </form>
    </div>
  </div> <!-- end view-wrapper -->
</body>
</html>