<?php
//
// Copyright (C) 2013 Kevin Varley at Emergent Ltd
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
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
  if(!isset($_SESSION['user']['Username'])) {
    die("Access denied.");
  }
  if(isset($_REQUEST['changepassword'])) {
    if((isset($_REQUEST['currentpassword']) && isset($_REQUEST['newpassword']) && isset($_REQUEST['newpasswordconfirmation']))&&(strlen($_REQUEST['currentpassword']) >= 5 && strlen($_REQUEST['newpassword']) >= 5 && strlen($_REQUEST['newpasswordconfirmation']) >= 5)) {
      $query = "SELECT Password FROM Users WHERE Id='" . $_SESSION['user']['Id'] . "' AND Password=PASSWORD('" . $_REQUEST['currentpassword'] . "')";
      $result = dbFetchOne($query);
      if(!$result) {
        die("ERROR: Database query error");
      }
      else {
        if($_REQUEST['newpassword'] === $_REQUEST['newpasswordconfirmation']) {
          $query = "UPDATE Users SET Password=PASSWORD('" . $_REQUEST['newpassword'] . "') WHERE Id='" . $_SESSION['user']['Id'] . "';";
          $result = dbQuery($query);
          if(!$result) {
            die("ERROR: Failed to change password");
          }
          else {
            echo "success";
          }
        }
        else {
          die("ERROR: New password confirmation does not match new password");
        }
      }
    }
    else {
      die("ERROR: New password must be at least 5 characters in length");
    }
  }
  else {
?>
    <div id="changepassword-panel" class="panel panel-primary">
      <div class="panel-heading">
        <h3 class="panel-title">Change Password</h3>
      </div>
      <div class="panel-body">
        <form action="?skin=modern&amp;view=changepassword" method="post" class="form-horizontal">
          <fieldset>
            <div class="form-group required-control">
              <label class="col-md-3 control-label" for="currentpassword">Current Password</label>
              <div class="col-md-6">
                <input id="currentpassword" name="currentpassword" type="password" placeholder="Enter your current password" class="form-control " required="" autofocus>
                
              </div>
            </div>

            <div class="form-group required-control">
              <label class="col-md-3 control-label" for="newpassword">New Password</label>
              <div class="col-md-6">
                <input id="newpassword" name="newpassword" type="password" placeholder="Enter your new password" class="form-control " required="">
                
              </div>
            </div>

            <div class="form-group required-control">
              <label class="col-md-3 control-label" for="newpasswordconfirmation">Confirm</label>
              <div class="col-md-6">
                <input id="newpasswordconfirmation" name="newpasswordconfirmation" type="password" placeholder="Enter your new password again" class="form-control " required="">
                
              </div>
            </div>

            <div class="form-group">
              <label class="col-md-3 control-label" for="changepassword"></label>
              <div class="col-lg-6">
                <button id="changepassword" name="changepassword" class="btn btn-primary">Change Password</button>
              </div>
            </div>
          </fieldset>
        </form>
      </div>
<?php
  }
?>