<?php

    // set up twig

    include 'php/twig.php';

    // set up connection to database via MySQLi

    include 'php/database.php';

    //echo "<script>alert('1st')</script>";
    if (!empty($_POST)) {
      //echo "<script>alert('The application failed to connect to the database!')</script>";
      $slots = json_decode($_POST['slotArray'], true);

      $stmtE = $database->prepare("INSERT INTO Event (name, description, fk_event_creator, location, capacity, open_slots) VALUES (?,?,?,?,?,?)");
      $stmtE->bind_param("ssisii", $eName, $eDesc, $eCreator, $eLocation, $cp, $oSlots);
      $eName = $_POST['eventName'];
      $eDesc = $_POST['eventDescription'];
      $eCreator = 2;
      $eLocation = $_POST['eventLocation'];
      $cp = 10;
      $oSlots = 10;

      $stmtE->execute();

      $last_id = $database->insert_id;



      $stmt = $database->prepare("INSERT INTO Timeslot (start_time, end_time, duration, slot_capacity, spaces_available, is_full, fk_event_id) VALUES (?,?,?,?,?,?,?)");
      $stmt->bind_param("ssiiiii", $val1, $val2, $val3, $val4, $val5, $val6, $val7);
      foreach($slots as $item){
        $sd = $item['startDate'];
        $ed = $item['endDate'];
        $val1 = $sd;
        $val2 = $ed;
        $val3 = 60;
        $val4 = 1;
        $val5 = 1;
        $val6 = 0;
        $val7 = $last_id;
        /* Execute the statement */
        $stmt->execute();

      }
      $stmt->close();
      echo "EVENTS have been submitted ";
      exit;

    }

     echo $twig -> render('views/event.twig');

?>
