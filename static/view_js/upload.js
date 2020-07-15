/******************************************************************
 * Upload.js
 *
 * This JavaScript file has refers to the file upload elements in the reservations_details page and register page.
 * This it's own page to help organize reusable code.
 *
 * A file can only be uploaded if an event has the file upload option enabled.
 *
 *********************************************************************/

// Upload a file to the DB from register or reservations_details page.
function uploadFile(fileData, slotKey) {
  var form_data = new FormData();

  form_data.append("file", fileData);
  form_data.append("slotKey", slotKey);

  $.ajax({
    url: "src/upload.php", // point to server-side PHP script
    dataType: "text", // what to expect back from the PHP script, if anything
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
    type: "POST"
  }).done(function (response) {
    alert(response);
    var sitePath = location.pathname;
    if (sitePath.search("/reservation_details.php") > 0 && response == 'Your file has been uploaded.') {
      location.reload();
    }
  });
}

// upload a file from manage page
function creatorUploadFile(fileData, eventKey) {
  var form_data = new FormData();

  form_data.append("file", fileData);
  form_data.append("eventKey", eventKey);

  $.ajax({
    url: "src/creator_upload.php", // point to server-side PHP script
    dataType: "text", // what to expect back from the PHP script, if anything
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
    type: "POST"
  }).done(function (response) {
    alert(response);
    var sitePath = location.pathname;
    if (sitePath.search("/manage.php") > 0 && response == 'Your file has been uploaded.') {
      location.reload();
    }
  });
}
