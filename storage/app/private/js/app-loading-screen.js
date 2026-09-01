(function () {
  function hideLoadingScreen() {
    var loadingScreen = document.getElementById('loading-screen');

    if (!loadingScreen) {
      return;
    }

    loadingScreen.style.opacity = '0';
    loadingScreen.style.pointerEvents = 'none';

    window.setTimeout(function () {
      loadingScreen.style.display = 'none';
    }, 200);
  }

  if (document.readyState === 'complete') {
    hideLoadingScreen();
  } else {
    window.addEventListener('load', hideLoadingScreen);
  }
})();
