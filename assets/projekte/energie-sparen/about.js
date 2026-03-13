document.documentElement.classList.add("about-html");

const reloadProject = document.getElementById("reloadProject");
const aboutScroll = document.getElementById("aboutScroll");

function nextFrame() {
  return new Promise((resolve) => requestAnimationFrame(resolve));
}

function revealApp() {
  document.documentElement.classList.remove("app-is-loading");
  document.documentElement.classList.add("app-is-ready");
}

function getViewportMetrics() {
  const vv = window.visualViewport;

  const layoutWidth = Math.max(
    window.innerWidth || 0,
    document.documentElement.clientWidth || 0
  );

  const layoutHeight = Math.max(
    window.innerHeight || 0,
    document.documentElement.clientHeight || 0
  );

  const dynamicHeight = vv ? Math.round(vv.height) : layoutHeight;

  return {
    width: layoutWidth,
    height: dynamicHeight
  };
}

function updateViewportCssVars() {
  const { width, height } = getViewportMetrics();
  document.documentElement.style.setProperty("--app-vw", `${width}px`);
  document.documentElement.style.setProperty("--app-vh", `${height}px`);
}

function resetScrollPosition() {
  if (aboutScroll) {
    aboutScroll.scrollTop = 0;
  }
}

function bindEvents() {
  if (reloadProject) {
    reloadProject.addEventListener("click", () => {
      window.location.href = "index.html";
    });
  }

  window.addEventListener("resize", updateViewportCssVars);

  if (window.visualViewport) {
    window.visualViewport.addEventListener("resize", updateViewportCssVars);
    window.visualViewport.addEventListener("scroll", updateViewportCssVars);
  }
}

async function initialize() {
  updateViewportCssVars();
  bindEvents();
  resetScrollPosition();

  if (document.fonts && document.fonts.ready) {
    await document.fonts.ready;
  }

  await nextFrame();
  revealApp();
}

initialize();