$(document).ready(function() {
  // Switch between NYSE and NASDAQ tables
  $(".nyse-button").click(function() {
    $(".nyse-button").addClass("active");
    $(".nasdaq-button").removeClass("active");
    // Update the URL to show only NYSE listings
    window.location.href = "?table=NYSE";
  });

  $(".nasdaq-button").click(function() {
    $(".nasdaq-button").addClass("active");
    $(".nyse-button").removeClass("active");
    // Update the URL to show only NASDAQ listings
    window.location.href = "?table=NASDAQ";
  });

  // Pagination functionality
  $(".pagination").on("click", ".page-link", function() {
    $(".page-link").removeClass("active");
    $(this).addClass("active");
    // Update the URL to show only the listings for the selected page
    var page = $(this).data("page");
    var currentURL = window.location.href;
    var queryString = "";
    if (currentURL.indexOf("?") != -1) {
      var baseURL = currentURL.split("?")[0];
      var parameters = currentURL.split("?")[1];
      var paramArray = parameters.split("&");
      for (var i = 0; i < paramArray.length; i++) {
        var parameter = paramArray[i].split("=");
        if (parameter[0] != "page") {
          queryString += paramArray[i] + "&";
        }
      } 
    } else {
      var baseURL = currentURL;
    }
    queryString += "page=" + page;
    window.location.href = baseURL + "?" + queryString;

  });
});
