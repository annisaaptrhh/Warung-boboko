$(document).ready(function () {
  $("#searchInput").keyup(function () {
    let query = $(this).val();
    $.ajax({
      url: "search.php",
      method: "GET",
      data: { query: query },
      success: function (data) {
        $("#searchResults").html(data);
      },
    });
  });
});
