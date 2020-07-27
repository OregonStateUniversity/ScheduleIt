$(document).ready(function () {
 // $("#createNav").addClass("activeNavItem"); // highlight the navbar for create
  initLocationInput(); // initialize location auto complete list
 // initDatePicker(); // Initialize the date picker
});

//source: https://blog.teamtreehouse.com/creating-autocomplete-dropdowns-datalist-element

function initLocationInput() {
  var dataList = document.getElementById("locationDatalist");
  var input = document.getElementById("locationInput");
  
  // create a new xmlhttprequest.
  var request = new XMLHttpRequest();

  // Handle state changes for the request.
  request.onreadystatechange = function (response) {
    if (request.readyState === 4) {
      if (request.status === 200) {
      // parse the JSON
      var jsonOptions = JSON.parse(request.responseText);

      // Loop over the JSON array.
      jsonOptions.forEach(function (item) {
        // Create a new <option> element.
        var option = document.createElement("option");

          // Set the value using the item in the JSON array.
          option.value = item.name;

          // Add the <option> element to the <datalist>
          dataList.appendChild(option);
        });

        // Update the placeholder text.
        input.placeholder = "Enter a Location";
      } else {
        // An error occured :(
        input.placeholder = "Couldn't load location options :(";
      }
    }
  };

  // Update the placeholder text.
  input.placeholder = "Loading options...";

  // Set up and make the request.
  request.open("GET", "../../resources/OSU_locations.json", true);
  request.send();
}
