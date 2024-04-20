//jQuery time
jQuery(document).ready(function () {
  // Add Tooltips
  const tooltipTriggerList = document.querySelectorAll(
    '[data-bs-toggle="tooltip"]'
  );
  const tooltipList = [...tooltipTriggerList].map(
    (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
  );

  // Get the value of the hidden field #wh_racks
  var whRacks = parseInt($("#wh_racks").val());

  // Select the "rack_all" select element
  var rackAllSelect = $("#rack_all");

  // Clear any existing options
  rackAllSelect.empty();

  // Add placeholder option
  rackAllSelect.append(
    $("<option>", {
      value: "",
      text: "Select rack",
      disabled: true,
      selected: true,
    })
  );

  // Populate the "rack_all" select with values from 1 to whRacks
  for (var i = 1; i <= whRacks; i++) {
    rackAllSelect.append(
      $("<option>", {
        value: i,
        text: i,
      })
    );
  }

  // Get the value of the hidden field #wh_racks
  var whRacks = parseInt($("#wh_racks").val());

  function generate_qr_code(url, id) {
    const qrcode = document.getElementById(id);

    const qr = new QRCode(qrcode);

    qr.makeCode(url);
  }

  $("#level_all, #rack_all, #amount_of_pallets, #quantity").on(
    "input",
    function () {
      var levelValue = $("#level_all").val();
      var rackValue = $("#rack_all").val();
      var palletsValue = $("#amount_of_pallets").val();
      var quantityValue = $("#quantity").val();

      // Check if all four inputs have values
      if (
        palletsValue !== "" &&
        quantityValue !== "" &&
        levelValue !== null &&
        rackValue !== null
      ) {
        var numberOfPallets = parseInt($("#amount_of_pallets").val());
        var dynamicRowsContainer = $("#dynamic-rows-container");
        var totalQuantity = parseInt($("#quantity").val());

        // Clear any existing rows
        dynamicRowsContainer.empty();
        var heading =
          '<h2 class="fs-title">Assign bins</h2><p>Assign a bin for each pallet in the warehouse</p>';

        dynamicRowsContainer.append(heading);

        if (numberOfPallets > 0) {
          var quantityPerBag = Math.floor(totalQuantity / numberOfPallets);
          var remainingQuantity = totalQuantity % numberOfPallets;
          for (var i = 0; i < numberOfPallets; i++) {
            let unique_pallet_id =
              i + Math.floor(Math.random() * 9000000) + 1000000;

            var newRow = $(
              '<div class="row">' +
                '<div class="col">' +
                '<div class="form-floating">' +
                '<input type="hidden" id="pallet_id' +
                i +
                '" name="pallet_id[]" class="form-control" value="' +
                unique_pallet_id +
                '">' +
                '<div id="qrcode' +
                i +
                '" name="qrcode' +
                i +
                '" class="qrcode"></div>' +
                '<p class="pallet_id_label"><span>Pallet ID</span><br>' +
                unique_pallet_id +
                "</p>" +
                "</div>" +
                "</div>" +
                '<div class="col">' +
                '<div class="form-floating">' +
                '<input type="number" id="amount_of_bags' +
                i +
                '" name="amount_of_bags[]" max="40" min="1" class="form-control" required value="' +
                quantityPerBag +
                '">' +
                '<label for="amount_of_bags' +
                i +
                '" class="form-label">Amount of bags:</label>' +
                "</div>" +
                "</div>" +
                '<div class="col">' +
                '<div class="form-floating">' +
                '<select id="rack' +
                i +
                '" name="rack[]" class="rack-select form-control" required></select>' +
                '<label for="rack' +
                i +
                '" class="form-label">Rack:</label>' +
                "</div>" +
                "</div>" +
                '<div class="col">' +
                '<div class="form-floating">' +
                '<select id="level' +
                i +
                '" name="level[]" class="form-control" required></select>' +
                '<label for="level' +
                i +
                '" class="form-label">Level:</label>' +
                "</div>" +
                "</div>" +
                '<div class="col">' +
                '<div class="form-floating">' +
                '<select id="position' +
                i +
                '" name="position[]" class="form-control" required></select>' +
                '<label for="position' +
                i +
                '" class="form-label">Bin ID:</label>' +
                "</div>" +
                "</div>"
            );

            dynamicRowsContainer.append(newRow);

            let qr_id = "qrcode" + i;
            const qrcode = document.getElementById(qr_id);

            // const qr = new QRCode(qrcode);

            const qr = new QRCode(qrcode, {
              text: "bin qr code",
              width: 100,
              height: 100,
              colorDark: "#000000",
              colorLight: "#ffffff",
              correctLevel: QRCode.CorrectLevel.L,
            });

            let qr_code_id = unique_pallet_id.toString();
            qr.makeCode(qr_code_id);

            // qr.makeCode(unique_pallet_id);

            // Populate the "Rack" dropdown with values 1 to whRacks
            populateDropdown("rack" + i, 1, whRacks);
          }

          // Listen for changes in the quantity input
          $("#quantity").on("input", function () {
            // Get the updated quantity value
            var newQuantity = parseInt($(this).val());
            $("#amount_of_pallets").val("");
            $("#rack_all").val("");
            $("#level_all").val("");
            $("#dynamic-rows-container").hide();

            // Recalculate and update the amount_of_bags for each row
            for (var i = 0; i < numberOfPallets; i++) {
              var quantityPerBag = Math.floor(newQuantity / numberOfPallets);
              var inputField = $("#amount_of_bags" + i);
              inputField.val(quantityPerBag);
            }
          });

          // Listen for changes in "rack" dropdowns for each row (excluding "rack_all")
          $('select[name^="rack"]:not(#rack_all)').on("change", function () {
            // Extract the row number from the select's name
            var selectedRack = $(this).val();

            var rowNumber = this.id.match(/\d+/);

            // Do something with the row number, e.g., console.log it
            console.log(
              "Rack dropdown in row " +
                rowNumber +
                " changed to value: " +
                $(this).val()
            );

            var whLevelsPerRack = $("#wh_levels_per_rack")
              .val()
              .split(",")
              .map(function (item) {
                return Number(item.trim());
              });

            // Calculate and update the "level" dropdown for the same row
            var levelsForSelectedRack = whLevelsPerRack[selectedRack - 1];
            var levelDropdown = $('select[id="level' + rowNumber + '"]');
            levelDropdown.empty(); // Clear existing options

            for (var i = 1; i <= levelsForSelectedRack; i++) {
              levelDropdown.append(
                $("<option>", {
                  value: i,
                  text: i,
                })
              );
            }

            // Calculate and update the "position" dropdown for the same row
            var positionsForSelectedRack = whCapacities[selectedRack - 1];
            var positionDropdown = $('select[id="position' + rowNumber + '"]');
            positionDropdown.empty(); // Clear existing options
            var selectedLevel = $('select[id="level' + rowNumber + '"]').val();

            // Get the value of the hidden field and split it into an array
            var binIds = document.getElementById("bin_ids").value.split(",");

            // Function to check if a bin_id exists
            function binIdExists(currentbinId) {
              return binIds.includes(currentbinId);
            }

            for (var i = 1; i <= positionsForSelectedRack; i++) {
              var label = selectedRack + "-" + selectedLevel + "-" + i;

              if (binIdExists(label)) {
                // Append the option with the formatted label
                positionDropdown.append(
                  $("<option>", {
                    value: "-",
                    text: "-",
                    disabled: true,
                  })
                );
              } else {
                // Append the option with the formatted label
                positionDropdown.append(
                  $("<option>", {
                    value: i,
                    text: label,
                  })
                );
              }
            }
          });

          // Listen for changes in "level" dropdowns for each row (excluding "level_all")
          $('select[name^="level"]:not(#level_all)').on("change", function () {
            // Extract the row number from the select's name
            var selectedLevel = $(this).val();

            var rowNumber = this.id.match(/\d+/);

            var selectedRack = $('select[id="rack' + rowNumber + '"]').val();

            // Calculate and update the "position" dropdown for the same row
            var positionsForSelectedRack = whCapacities[selectedRack - 1];
            var positionDropdown = $('select[id="position' + rowNumber + '"]');
            positionDropdown.empty(); // Clear existing options

            for (var i = 1; i <= positionsForSelectedRack; i++) {
              var label = selectedRack + "-" + selectedLevel + "-" + i;
              positionDropdown.append(
                $("<option>", {
                  value: i,
                  text: label,
                })
              );
            }
          });

          // Distribute any remaining quantity to the first rows
          for (var i = 0; i < remainingQuantity; i++) {
            var inputField = $("#amount_of_bags" + i);
            var currentValue = parseInt(inputField.val());
            inputField.val(currentValue + 1);
          }
        } else {
          // Hide the dynamic rows if the number of pallets is not greater than 0
          dynamicRowsContainer.hide();
        }
      }
    }
  );

  // Function to populate a dropdown with values from minValue to maxValue
  function populateDropdown(elementId, minValue, maxValue) {
    var dropdown = $("#" + elementId);

    // Populate rest of the options
    for (var value = minValue; value <= maxValue; value++) {
      dropdown.append(
        $("<option>", {
          value: value,
          text: value,
        })
      );
    }
  }

  // Get the value of the hidden field #wh_racks
  var whRacks = parseInt($("#wh_racks").val());

  // Listen for changes in the "rack_all" select
  $("#rack_all").on("change", function () {
    var selectedRack = parseInt($(this).val());

    $("#dynamic-rows-container").hide();

    // Set the default value for all "Rack" dropdowns based on selected rack_all
    $('select[name^="rack"]').val(selectedRack);
  });

  // Listen for changes in the "rack_all" select
  $("#level_all").on("change", function () {
    var selectedLevel = parseInt($(this).val());
    $("#dynamic-rows-container").show();

    // Set the default value for all "Level" dropdowns based on selected level_all
    $('select[name^="level"]').val(selectedLevel);
  });

  // Trigger the change event on "rack_all" to initialize the values
  $("#rack_all").change();

  // Initialize dropdowns on page load
  $(document).ready(function () {
    $("#rack_all").trigger("change");
  });

  // Get the value of the hidden field #wh_levels_per_rack
  var whLevelsPerRack = $("#wh_levels_per_rack").val().split(", ").map(Number);

  // Listen for changes in the "rack_all" select
  $("#rack_all").on("change", function () {
    var selectedRack = parseInt($(this).val());

    // Get the corresponding number of levels based on the selected rack
    var levelsForSelectedRack = whLevelsPerRack[selectedRack - 1];

    // Select the "level_all" select element
    var levelAllSelect = $("#level_all");

    // Clear any existing options
    levelAllSelect.empty();
    // levelSelect.empty();

    // Add placeholder option
    levelAllSelect.append(
      $("<option>", {
        value: "",
        text: "Select level",
        disabled: true,
        selected: true,
      })
    );

    // Populate the "level_all" and "levels" select with values from 1 to levelsForSelectedRack
    for (var i = 1; i <= levelsForSelectedRack; i++) {
      levelAllSelect.append(
        $("<option>", {
          value: i,
          text: i,
        })
      );
    }
  });

  // Get the value of the hidden field #wh_capacities
  var whCapacities = $("#wh_capacities")
    .val()
    .split(",")
    .map(function (item) {
      return Number(item.trim());
    });

  // Listen for changes in the "rack_all" select
  $("#rack_all").on("change", function () {
    updatePositions();
  });

  // Listen for changes in the "level_all" select
  $("#level_all").on("change", function () {
    updatePositions();
    $('select[name^="level"]').val(parseInt($(this).val()));
  });

  function updatePositions() {
    var selectedLevel = parseInt($("#level_all").val());
    var selectedRack = parseInt($("#rack_all").val());

    $('select[name^="rack"]').val(selectedRack);

    var positionsForSelectedRack = $("#wh_capacities")
      .val()
      .split(", ")
      .map(Number)[selectedRack - 1];

    var binIds = document.getElementById("bin_ids").value.split(",");

    function binIdExists(currentbinId) {
      return binIds.includes(currentbinId);
    }

    // Function to get all currently selected positions
    function getSelectedPositions() {
      var selectedPositions = [];
      $('select[name^="position"]').each(function () {
        var value = parseInt($(this).val());
        if (!isNaN(value)) {
          selectedPositions.push(value);
        }
      });
      return selectedPositions;
    }

    var previousStartingPosition = 0;

    $('select[name^="position"]').each(function (index) {
      var currentSelectedValue = parseInt($(this).val());
      $(this).empty(); // Clear existing options

      // The starting position for this row is one more than the previous row's starting position
      var startingPosition = previousStartingPosition + 1;
      var firstValidOption = null;
      var selectedPositions = getSelectedPositions(); // Get selected positions in other rows

      for (var i = startingPosition; i <= positionsForSelectedRack; i++) {
        if (isNaN(selectedLevel)) {
          selectedLevel = "Select level";
          $("#goods-form > input.submit.action-button").attr("disabled", true);
        }
        var label = selectedRack + "-" + selectedLevel + "-" + i;
        var isUnavailable =
          binIdExists(label) ||
          (selectedPositions.includes(i) && i !== currentSelectedValue);

        $(this).append(
          $("<option>", {
            value: i,
            text: label,
            disabled: isUnavailable,
          })
        );

        if (firstValidOption === null && !isUnavailable) {
          firstValidOption = i;
        }
      }

      if (
        firstValidOption !== null &&
        !isNaN(currentSelectedValue) &&
        currentSelectedValue !== firstValidOption
      ) {
        // Keep the previously selected value if it's still valid
        $(this).val(currentSelectedValue);
      } else {
        // Otherwise, set to the first valid option
        // $(this).val(firstValidOption);
        // previousStartingPosition = firstValidOption; // Update the previousStartingPosition
      }
    });
  }

  // Call updatePositions to initialize
  updatePositions();

  // Trigger the change event on "rack_all" and "level_all" to initialize the values
  $("#rack_all, #level_all").change();

  // Listen for changes in the "level_all" select
  $("#level_all").on("change", function () {
    var selectedLevelAll = parseInt($(this).val());

    // Get the options from the "level_all" select
    var levelAllOptions = $("#level_all").children();

    // Select all "level" selects and update their options
    $('select[name^="level"]').each(function () {
      $(this).empty(); // Clear existing options

      // Populate the "level" select with options from "level_all"
      levelAllOptions.clone().appendTo(this);
      $(this).val(selectedLevelAll);
    });
  });
});

$(document).ready(function () {
  // Assuming your form has an ID 'yourFormId'
  $("#goods-form").on("submit", function (e) {
    var selectedPositions = [];
    var isDuplicate = false;

    $('select[name^="position"]').each(function () {
      var selectedValue = $(this).val();
      const labelText = $('label[for="' + this.id + '"]').text();
      var val = selectedRack + "-" + selectedValue + "-" + selectedLevel;
      console.log(val);
      if (selectedPositions.includes(val)) {
        isDuplicate = true;
        return false; // Break the loop
      }
      selectedPositions.push(val);
      console.log(selectedPositions);
    });

    if (isDuplicate) {
      e.preventDefault(); // Prevent form submission
      alert(
        "Duplicate positions selected. Please select different positions for each field."
      );
      Swal.fire({
        icon: "error",
        title:
          "Duplicate positions selected. Please select different positions for each field.",
      });
    }
  });
});

function myFunction(id, warehouse_id) {
  // Making an AJAX request
  $.ajax({
    url: my_script_data.ajaxurl, // WordPress defines this variable for you, it points to /wp-admin/admin-ajax.php
    type: "POST",
    data: {
      action: "get_bin_details", // WordPress AJAX hook action
      bin_id: id, // the ID to pass to PHP
      warehouse_id: warehouse_id, // the ID to pass to PHP
    },
    success: function (response) {
      if (
        (response.status === "success" && response.bin_status == 1) ||
        response.bin_status == 2 ||
        response.bin_status == 3
      ) {
        // Create HTML string from received data
        let messageHtml = `<b>Product Name</b>: ${response.message.product_name}<br>`;
        messageHtml += `<b>Batch Number</b>: ${response.message.batch_number}<br>`;
        messageHtml += `<b>Expiry Date</b>: ${response.message.expiry_date}<br>`;
        messageHtml += `<b>Pallet ID</b>: ${response.message.pallet_id}<br>`;
        messageHtml += `<b>Quantity</b>: ${response.message.quantity}<br>`;
        let total = (response.message.quantity / 40) * 100;
        messageHtml += `<br><div class="progress" role="progressbar" aria-label="Example with label" aria-valuenow="${response.message.quantity}" aria-valuemin="0" aria-valuemax="${response.message.quantity}">
        <div class="progress-bar" style="width: ${total}%">${response.message.quantity}</div>
    </div>`;

        let titleText = "";

        if (response.bin_status == 2) {
          titleText = "Bin Reserved";
        } else if (response.bin_status == 3) {
          titleText = "Bin Locked";
        } else {
          titleText = `Details for Bin ID ${id}`;
        }

        Swal.fire({
          icon: "info",
          title: titleText,
          html: messageHtml,
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "No product found in bin",
          text: "Bin is empty",
        });
      }
    },
    error: function () {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Something went wrong!",
      });
    },
  });
}

$(document).ready(function () {
  // When a tab is clicked
  $('a[data-bs-toggle="tab"]').on("click", function () {
    // Save the latest tab using a data attribute, 'id'
    var activeTab = $(this).attr("id");
    localStorage.setItem("activeWarehouseTab", activeTab);
  });

  // On page load
  var activeTab = localStorage.getItem("activeWarehouseTab");
  if (activeTab) {
    $("#" + activeTab).tab("show");
  }
});

function addToPickingList(binId, good_received_id) {
  // Making an AJAX request
  $.ajax({
    url: my_script_data.ajaxurl, // WordPress defines this variable for you, it points to /wp-admin/admin-ajax.php
    type: "POST",
    data: {
      action: "add_bin_to_picking_list", // WordPress AJAX hook action
      bin_id: binId,
      good_received_id: good_received_id,
    },
    success: function (response) {
      if (response.status === "success") {
        // Create HTML string from received data

        Swal.fire({
          position: "top-end",
          icon: "success",
          title: "Success",
          html: "<p>Bin successfully added to picking list.</p>",
          showConfirmButton: false,
          timer: 1500,
        }).then(function () {
          location.reload();
        });
      } else {
        alert("Error adding bin to picking list");
      }
    },
    error: function () {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Something went wrong!",
      }).then(function () {
        location.reload();
      });
    },
  });
}

function removeFromPickingList(binId) {
  // Making an AJAX request
  $.ajax({
    url: my_script_data.ajaxurl, // WordPress defines this variable for you, it points to /wp-admin/admin-ajax.php
    type: "POST",
    data: {
      action: "remove_bin_from_picking_list", // WordPress AJAX hook action
      bin_id: binId,
    },
    success: function (response) {
      if (response.status === "success") {
        // Create HTML string from received data

        Swal.fire({
          position: "top-end",
          icon: "success",
          title: "Success",
          html: "<p>Bin successfully removed from picking list.</p>",
          showConfirmButton: false,
          timer: 1500,
        }).then(function () {
          location.reload();
        });
      } else {
        alert("Error removing bin from picking list");
        console.log(response);
      }
    },
    error: function () {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Something went wrong!",
      });
    },
  });
}

function completeMovement(movement_list_id, user_id, picking_list_id) {
  Swal.fire({
    title: "Are you sure?",
    text: "Do you confirm all movement into warehouse for this list?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Confirmed",
  }).then((result) => {
    if (result.isConfirmed) {
      // Making an AJAX request
      $.ajax({
        url: my_script_data.ajaxurl,
        type: "POST",
        data: {
          action: "complete_move",
          movement_list_id: movement_list_id,
          picking_list_id: picking_list_id,
          current_user: user_id,
        },
        success: function (response) {
          if (response.status === "success") {
            let messageHtml = `<b>${movement_list_id}</b> has been completed and moved into warehouse<br>`;
            console.log(response.picking_list);
            Swal.fire({
              icon: "info",
              title: `${movement_list_id}`,
              html: messageHtml,
            }).then(() => {
              // Refresh the page after clicking 'OK' in the success dialog
              location.reload();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "No product found",
              text: response.message,
            });
          }
        },
        error: function () {
          Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Something went wrong!",
          });
        },
      });
    }
  });
}

function cancelMovement(movement_list_id, user_id) {
  // Swal.fire({
  //   title: "Are you sure?",
  //   text:
  //     "Do you confirm you would like to cancel the movement for " +
  //     movement_list_id +
  //     "?",
  //   icon: "warning",
  //   showCancelButton: true,
  //   confirmButtonColor: "#d33",
  //   cancelButtonColor: "#000",
  //   confirmButtonText: "Cancel Movement",
  //   cancelButtonText: "Close",
  // }).then((result) => {
  //   if (result.isConfirmed) {
  //     // Making an AJAX request
  //     $.ajax({
  //       url: my_script_data.ajaxurl,
  //       type: "POST",
  //       data: {
  //         action: "cancel_move",
  //         movement_list_id: movement_list_id,
  //         current_user: user_id,
  //       },
  //       success: function (response) {
  //         console.log(response);
  //         if (response.status === "success") {
  //           let messageHtml = `<b>${response.message}</b><br>`;
  //           Swal.fire({
  //             icon: "info",
  //             title: `${movement_list_id}`,
  //             html: `${movement_list_id} as been canceled and bins are no longer reserved`,
  //           }).then(() => {
  //             // Refresh the page after clicking 'OK' in the success dialog
  //             location.reload();
  //           });
  //         } else {
  //           Swal.fire({
  //             icon: "error",
  //             title: "No product found",
  //             text: response.message,
  //           });
  //         }
  //       },
  //       error: function () {
  //         Swal.fire({
  //           icon: "error",
  //           title: "Oops...",
  //           text: "Something went wrong!",
  //         });
  //       },
  //     });
  //   }
  // });

  Swal.fire({
    title: "Are you sure?",
    input: "text",
    inputAttributes: {
      autocapitalize: "off",
    },
    text:
      "Do you confirm you would like to cancel the movement for " +
      movement_list_id +
      "?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#000",
    confirmButtonText: "Cancel Movement",
    cancelButtonText: "Close",
    showLoaderOnConfirm: true,
    preConfirm: (reason) => {
      return $.ajax({
        url: my_script_data.ajaxurl, // Replace with your endpoint URL
        type: "POST",
        data: {
          action: "cancel_move",
          reason: reason,
          movement_list_id: movement_list_id,
          current_user: user_id,
        },
        success: function (response) {
          console.log(response);
          if (response.status === "success") {
            let messageHtml = `<b>${response.message}</b><br>`;
            Swal.fire({
              icon: "info",
              title: `${movement_list_id}`,
              html: `${movement_list_id} as been canceled and bins are no longer reserved`,
            }).then(() => {
              // Refresh the page after clicking 'OK' in the success dialog
              location.reload();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "No product found",
              text: response.message,
            });
          }
        },
        error: function (xhr) {
          Swal.showValidationMessage(`Request failed: ${xhr.statusText}`);
        },
      });
    },
    allowOutsideClick: () => !Swal.isLoading(),
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: `Submitted!`,
        html: `Your name: <strong>${response.message}</strong>`,
      });
    }
  });
}

function movementList(movement_list_id, warehouse_id, picking_list_id) {
  // Making an AJAX request
  $.ajax({
    url: my_script_data.ajaxurl, // WordPress defines this variable for you, it points to /wp-admin/admin-ajax.php
    type: "POST",
    data: {
      action: "movement_list", // WordPress AJAX hook action
      movement_list_id: movement_list_id, // the ID to pass to PHP
      warehouse_id: warehouse_id,
      picking_list_id: picking_list_id,
    },
    success: function (response) {
      if (response) {
        const data = JSON.parse(response.list_items);
        const picking_list = JSON.parse(response.picking_list_items);
        console.log("picking", picking_list);
        console.log("data", data);
        console.log(response.sql);

        let messageHtml = `<div id="root"><table class="table mb-0 table-striped table-sm">
        <thead>
            <tr>
                <th scope="col">Pallet ID</th>
                <th scope="col">From</th>
                <th scope="col">To</th>
                <th scope="col">Product name</th>
                <th scope="col">Units</th>
                <th scope="col">Batch</th>
                <th scope="col">Expiry date</th>
            </tr>
        </thead>
        <tbody>`;
        if (picking_list === null || picking_list.length == 0) {
          data.forEach(function (item) {
            messageHtml += `<tr>
                  <th scope="row">${item.pallet_id}</th>
                  <td scope="row">${item.bay_name}</td>
                  <td scope="row">${item.bin_id}</td>
                  <td>${item.post_title}</td>
                  <td>${item.bag_total}</td>
                  <td>${item.custom_input}</td>
                  <td>${item.expiry_date}</td>
              </tr>`;
          });
        } else {
          picking_list.forEach(function (item) {
            data.forEach(function (data) {
              // Access item details through the "0" key.
              const itemDetails = item["0"];
              const goodsDetails = item["goods_info"];
              console.log(goodsDetails.pallet_id);

              messageHtml += `<tr>
                    <th scope="row">${goodsDetails.pallet_id}</th>
                    <td>${itemDetails.bin_id}</td>
                    <td>${goodsDetails.bay_name}</td> <!-- Correctly accessing bay_name here -->
                    <td>${goodsDetails.product_name_text}</td>
                    <td>${itemDetails.quantity}</td>
                    <td>${goodsDetails.custom_input}</td>
                    <td>${goodsDetails.expiry_date}</td>
                </tr>`;
            });
          });
        }

        if (data[0].movement_status == 1) {
          messageHtml += `<tr>
              <th scope="col">Created by</td>
              <td colspan='6'>${data[0].created_by}</th>`;
          messageHtml += `</tr>`;
        } else {
          messageHtml += `<tr>
          <th scope="col">Created by</td>
          <td colspan='2'>${data[0].created_by}</th>`;
          if (data[0].movement_status == 2) {
            messageHtml += `<th scope="col">Cancelled by</td>`;
            messageHtml += `<td colspan='1'>${data[0].confirmed_by}</th>`;
          } else {
            messageHtml += `<th scope="col">Confirmed by</td>`;
            messageHtml += `<td colspan='1'>${data[0].confirmed_by}</th>`;
          }
          messageHtml += `</tr>`;
        }

        messageHtml += `</tbody>
            </table></div>`;

        let titleText = "";

        if (response.bin_status == 2) {
          titleText = "Bin Reserved";
        } else {
          titleText = `Details for movement: ${movement_list_id}`;
        }

        Swal.fire({
          icon: "info",
          title: titleText,
          customClass: "swal-wide",
          html: messageHtml,
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "No product found",
          text: response.message,
        });
      }
    },
    error: function () {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Something went wrong!",
      });
    },
  });
}

$(document).ready(function () {
  // Initialize DataTables with initComplete callback
  $("#table_search").DataTable({
    dom: "Bfrtlip",
    buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5", "print"],
    colReorder: true,
    pageLength: 40,
  });
  $("#all_movement_list_table").DataTable({
    dom: "Bfrtlip",
    buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
    order: [[4, "desc"]],
  });
  $("#table_expiring_products").DataTable({
    dom: "Bfrtlip",
    buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
  });
  // Modify button classes
  $(".dt-buttons button")
    .removeClass("btn btn-secondary")
    .addClass("btn btn-outline-primary");
});

function search_svg(post_id) {
  // Making an AJAX request
  let search_query = $("#searchQuery").val();
  console.log(search_query);
  $.ajax({
    url: my_script_data.ajaxurl, // WordPress defines this variable for you, it points to /wp-admin/admin-ajax.php
    type: "POST",
    data: {
      action: "get_search_svg", // WordPress AJAX hook action
      post_id: post_id, // the ID to pass to PHP
      search_query: search_query,
    },
    success: function (response) {
      if (response.status === "success") {
        // Create HTML string from received data

        $("#layout-pane").html(response.svg);
        $("#filter-text").html(
          '<span class="badge rounded-pill text-bg-primary">' +
            search_query +
            "</span>"
        );
        $("#Table_ID_filter input").val(search_query);
      } else {
        Swal.fire({
          icon: "error",
          title: "No product found",
          text: response.messsage,
        });
      }
    },
    error: function () {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Something went wrong!",
      });
    },
  });
}

// Function to check URL parameters and show the tab
function checkUrlAndShowTab() {
  var urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has("move") && urlParams.has("id")) {
    $("#receive-goods-tab").tab("show");
  }
}

// Call the function when the document is ready
$(document).ready(function () {
  checkUrlAndShowTab();
});

// moveBin function to change the URL
function moveBin(id) {
  var newUrl =
    window.location.protocol +
    "//" +
    window.location.host +
    window.location.pathname +
    "?move&id=" +
    id;
  window.location.href = newUrl;
}

$(window).on("load", function () {
  // Calculate the height of the content
  const contentHeight = document.querySelector(".page-break").offsetHeight;

  // Set the maximum height of each page (adjust as needed)
  const maxHeightPerPage = 800; // For example, assuming each page can hold up to 800px of content

  // Calculate the number of pages needed
  const totalPages = Math.ceil(contentHeight / maxHeightPerPage);

  var doc = new jsPDF();

  var specialElementHandlers = {
    "#editor": function (element, renderer) {
      return true;
    },
  };

  var margin = {
    top: 50,
    left: 50,
    right: 50,
    bottom: 50,
  };

  $("#pdfview").click(function () {
    doc.fromHTML(
      $("#pdf").html(),
      15,
      15,
      {
        width: 100,
        elementHandlers: specialElementHandlers,
      },
      function (bla) {
        doc.save("label.pdf");
      },
      margin
    );
  });
});

$(document).ready(function () {
  let btn = $("#c-oreder-preview");
  btn.text("download");
  btn.on("click", () => {
    $("#c-invoice").modal("show");
    setTimeout(function () {
      html2canvas(document.querySelector("#pdf")).then((canvas) => {
        //$("#previewBeforeDownload").html(canvas);
        var imgData = canvas.toDataURL("image/jpeg", 1);
        var pdf = new jsPDF("p", "mm", "a4");
        var pageWidth = pdf.internal.pageSize.getWidth();
        var pageHeight = pdf.internal.pageSize.getHeight();
        var imageWidth = canvas.width;
        var imageHeight = canvas.height;

        var ratio =
          imageWidth / imageHeight >= pageWidth / pageHeight
            ? pageWidth / imageWidth
            : pageHeight / imageHeight;
        //pdf = new jsPDF(this.state.orientation, undefined, format);
        pdf.addImage(
          imgData,
          "JPEG",
          0,
          0,
          imageWidth * ratio,
          imageHeight * ratio
        );
        pdf.save("invoice.pdf");
        //$("#previewBeforeDownload").hide();
        $("#c-invoice").modal("hide");
      });
    }, 500);
  });
});

function printDiv(divId, title) {
  let mywindow = window.open(
    "",
    "PRINT",
    "height=650,width=900,top=100,left=150"
  );

  mywindow.document.write(`<html><head><title>${title}</title>`);
  mywindow.document.write("</head><body >");
  mywindow.document.write(document.getElementById(divId).innerHTML);
  mywindow.document.write("</body></html>");

  mywindow.document.close(); // necessary for IE >= 10
  mywindow.focus(); // necessary for IE >= 10*/

  mywindow.print();
  mywindow.close();

  return true;
}

function generateQRcode(id, pallet_id) {
  const qrcode = document.getElementById(id);

  const qr = new QRCode(qrcode, {
    text: "bin qr code",
    width: 500,
    height: 500,
    colorDark: "#000000",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.L,
  });

  let qr_code_id = pallet_id.toString();
  qr.makeCode(qr_code_id);
}

// Initialize select2 on the dropdown element
jQuery(document).ready(function ($) {
  $("#product_name").select2({
    placeholder: "Search for a product",
  });
});

function goBack() {
  window.history.back();
}

function selectCard(selectedType) {
  var externalCard = document.getElementById("externalCard");
  var internalCard = document.getElementById("internalCard");

  var externalTable = document.getElementById("external");
  var internalTable = document.getElementById("internal");
  var tabl_header_label = document.getElementById("tabl_header_label");

  // Remove 'selected' class from both cards
  externalCard.classList.remove("selected");
  internalCard.classList.remove("selected");

  // Add 'selected' class to the clicked card
  if (selectedType === "external") {
    internalTable.style.display = "none";
    externalTable.style.display = "block";
    externalCard.classList.add("selected");
  } else if (selectedType === "internal") {
    externalTable.style.display = "none";
    internalTable.style.display = "block";
    internalCard.classList.add("selected");
  }
}
