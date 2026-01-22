document.getElementById('driverSearch').addEventListener('input', function () {
  let query = this.value;

  if (query.length > 2) {
    fetch('search.php?query=' + query)
      .then((response) => response.text())
      .then((data) => {
        document.getElementById('results').innerHTML = data;
      });
  } else {
    document.getElementById('results').innerHTML = '';
  }
});
