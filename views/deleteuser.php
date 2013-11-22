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
  if(isset($_REQUEST['deleteuser'])) {
    if((isset($_REQUEST['uid']))&&(ctype_digit($_REQUEST['uid']))) {
      $response = dbQuery("DELETE FROM Users WHERE Id='$_REQUEST[uid]'");
      if(!$response) {
        die("ERROR: Failed to delete user from database!");
      }
      else {
        echo "success";
      }
    }
   }
?>