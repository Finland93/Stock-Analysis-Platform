// Get the NYSE and NASDAQ buttons
var nyseButton = document.querySelector(".nyse-button");
var nasdaqButton = document.querySelector(".nasdaq-button");

// Set the default values for P/E and P/B
var pe = nysePe;
var pb = nysePb;

// Add a click event to the NYSE button
nyseButton.addEventListener("click", function() {
pe = nysePe;
pb = nysePb;
});

// Add a click event to the NASDAQ button
nasdaqButton.addEventListener("click", function() {
pe = nasdaqPe;
pb = nasdaqPb;
});

// Get the table cells with the sector averages
var nyseRow = document.querySelector("table tr:nth-child(2)");
var nasdaqRow = document.querySelector("table tr:nth-child(3)");

// Get the P/E and P/B ratio cells
var nysePeCell = nyseRow.querySelector("td:nth-child(2)");
var nasdaqPeCell = nasdaqRow.querySelector("td:nth-child(2)");
var nysePbCell = nyseRow.querySelector("td:nth-child(3)");
var nasdaqPbCell = nasdaqRow.querySelector("td:nth-child(3)");

// Extract the values from the cells and store them in variables
var nysePe = parseFloat(nysePeCell.textContent);
var nasdaqPe = parseFloat(nasdaqPeCell.textContent);
var nysePb = parseFloat(nysePbCell.textContent);
var nasdaqPb = parseFloat(nasdaqPbCell.textContent);

// Get the display button
var displayButton = document.querySelector(".display-button");

// Add a click event to the display button
displayButton.addEventListener("click", function() {
  var rows = document.querySelectorAll(".stock-listings-data tr");

  // Loop through all rows
  for (var i = 0; i < rows.length; i++) {
    var row = rows[i];

    // Get the cells of the row
    var peRatioCell = row.querySelector("td:nth-child(4)");
    var pbRatioCell = row.querySelector("td:nth-child(5)");
    var rsiAnalysisCell = row.querySelector("td:nth-child(6)");

    // Store the original cell content as numbers
    var peRatioNumber;
    var pbRatioNumber;
    var rsiAnalysisNumber;

    try {
      peRatioNumber = parseFloat(peRatioCell.textContent);
      pbRatioNumber = parseFloat(pbRatioCell.textContent);
      rsiAnalysisNumber = parseFloat(rsiAnalysisCell.textContent);
    } catch (e) {
      console.error("Error parsing cell content:", e);
      continue;
    }

    // Replace the cell content with images or numbers based on the conditions
if (peRatioCell.classList.contains("modified")) {
  // Replace the cell content with numbers
  peRatioCell.innerHTML = '<span class="sortNumber">' + peRatioNumber + '</span>';
  peRatioCell.classList.remove("modified");
} else {
  // Replace the cell content with images
  if (peRatioNumber >= nysePe + 10 || peRatioNumber >= nasdaqPe + 10) {
    peRatioCell.innerHTML = '<span class="sortNumber" style="display:none">' + peRatioNumber + '</span><img class="sortImg" title="Average P/E + 10" src="img/sell.png" style="display:inline-block"/>';
  } else if (peRatioNumber <= nysePe - 10 || peRatioNumber <= nasdaqPe - 10) {
    peRatioCell.innerHTML = '<span class="sortNumber" style="display:none">' + peRatioNumber + '</span><img class="sortImg" title="Average P/E - 10" src="img/buy.png" style="display:inline-block"/>';
  } else {
    peRatioCell.innerHTML = '<span class="sortNumber" style="display:none">' + peRatioNumber + '</span><img class="sortImg" title="Average P/E between +10 and -10" src="img/hold.png" style="display:inline-block"/>';
  }
  peRatioCell.classList.add("modified");
}

// Replace the cell content with images or numbers based on the conditions
if (pbRatioCell.classList.contains("modified")) {
  // Replace the cell content with numbers
  pbRatioCell.innerHTML = '<span class="sortNumber">' + pbRatioNumber + '</span>';
  pbRatioCell.classList.remove("modified");
} else {
  // Replace the cell content with images
  if (pbRatioNumber >= nysePb + 1.5 || pbRatioNumber >= nasdaqPb + 1.5) {
    pbRatioCell.innerHTML = '<span class="sortNumber" style="display:none">' + pbRatioNumber + '</span><img class="sortImg"  title="Average P/B + 1,5" src="img/sell.png" style="display:inline-block"/>';
  } else if (pbRatioNumber <= nysePb - 1.5 || pbRatioNumber <= nasdaqPb - 1.5) {
    pbRatioCell.innerHTML = '<span class="sortNumber" style="display:none">' + pbRatioNumber + '</span><img class="sortImg" title="Average P/B - 1,5" src="img/buy.png" style="display:inline-block"/>';
  } else {
    pbRatioCell.innerHTML = '<span class="sortNumber" style="display:none">' + pbRatioNumber + '</span><img class="sortImg" title="Average P/B between +1,5 and -1,5" src="img/hold.png" style="display:inline-block"/>';
  }
  pbRatioCell.classList.add("modified");
}

if (rsiAnalysisCell.classList.contains("modified")) {
  // Replace the cell content with numbers
  rsiAnalysisCell.innerHTML = '<span class="sortNumber">' + rsiAnalysisNumber + '</span>';
  rsiAnalysisCell.classList.remove("modified");
} else {
  // Replace the cell content with images
  if (rsiAnalysisNumber >= 70) {
    rsiAnalysisCell.innerHTML = '<span class="sortNumber" style="display:none">' + rsiAnalysisNumber + '</span><img class="sortImg" title="RSI Above 70" src="img/sell.png" style="display:inline-block"/>';
  } else if (rsiAnalysisNumber <= 30) {
    rsiAnalysisCell.innerHTML = '<span class="sortNumber" style="display:none">' + rsiAnalysisNumber + '</span><img class="sortImg" title="RSI Below 30" src="img/buy.png" style="display:inline-block"/>';
  } else {
    rsiAnalysisCell.innerHTML = '<span class="sortNumber" style="display:none">' + rsiAnalysisNumber + '</span><img class="sortImg" title="RSI Between 30-70" src="img/hold.png" style="display:inline-block"/>';
  }
  rsiAnalysisCell.classList.add("modified");
}

}
});