document.getElementById('liveSearch').addEventListener('keyup', function () {
  let q = this.value;
  if (q.length > 1) {
    fetch('search.php?q=' + q)
      .then((res) => res.text())
      .then(
        (data) => (document.getElementById('searchResults').innerHTML = data),
      );
  } else {
    document.getElementById('searchResults').innerHTML = '';
  }
});
