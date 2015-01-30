<?php
    echo "<div id=\"content\"></div>";
    echo "<script>";
    echo "Event.observe(window, 'load', function(){";
      
      echo "studioGUI.start('".$domain."')});";

    echo "</script>";
?>