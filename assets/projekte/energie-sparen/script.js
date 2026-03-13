const app = document.getElementById("app");
const world = document.getElementById("world");
const chapters = [...document.querySelectorAll(".chapter")];
const navItems = [...document.querySelectorAll(".nav-item")];
const reloadProject = document.getElementById("reloadProject");
const titleChapter = document.querySelector(".title-chapter");
const chapterVideoHosts = [...document.querySelectorAll(".chapter-inline-video")];
const videoProgressFills = [...document.querySelectorAll(".video-progress-fill")];
const soundToggle = document.getElementById("soundToggle");
const fullscreenToggle = document.getElementById("fullscreenToggle");

const WORLD_WIDTH = 8800;
const WORLD_HEIGHT = 3000;
const FOCUS_SCALE = 1;
const TILE_SIZE = 320;

const MOBILE_BREAKPOINT = 950;
const VIEWPORT_REPOSITION_THRESHOLD = 60;
const CONTROLS_VISIBLE_MS = 1000;
const PLAYER_READY_TIMEOUT_MS = 8000;
const DEFAULT_TEST_VIDEO_ID = "PJ8DCYkS9wU";

const YOUTUBE_VIDEO_IDS = {
  1: "Wbt7wO2fLYM",
  2: "0hjIoHPOBiI",
  3: "TROpwplbpos",
  4: "zQc18fUUGf8",
  5: "TtFibV5RZmo"
};

const stations = [
  { x: 900, y: 1450, label: "Titel" },
  { x: 2100, y: 1450, label: "Kapitel 1" },
  { x: 3300, y: 1450, label: "Kapitel 2" },
  { x: 4500, y: 1450, label: "Kapitel 3" },
  { x: 5700, y: 1450, label: "Kapitel 4" },
  { x: 6900, y: 1450, label: "Kapitel 5" },
  { x: 8100, y: 1450, label: "Ende" }
];

const chapterDots = chapters.map((chapter) => chapter.querySelector(".chapter-dot"));
const chapterPlayButtons = new Map(
  chapters
    .map((chapter) => [Number(chapter.dataset.index), chapter.querySelector(".chapter-play-button")])
    .filter(([, button]) => button)
);

const playerHostMap = new Map(
  chapterVideoHosts.map((host) => [Number(host.dataset.chapter), host])
);

const youtubePlayers = new Map();
const playerStates = new Map();
const playerDurations = new Map();
const playerReady = new Map();
const playerProgressIntervals = new Map();

let currentIndex = 0;
let currentScale = FOCUS_SCALE;
let isAnimating = false;
let titleDismissed = false;
let completedChapters = new Set();
let unlockedIndex = 1;
let isMuted = false;
let activeVideoChapter = null;

let mobileVideoFullscreenChapter = null;
let lastViewportWidth = 0;
let lastViewportHeight = 0;
let layoutUpdateRaf = null;
let resizeSettleTimer = null;
let controlsHideTimeout = null;
let autoAdvanceTimeout = null;
let playersCreated = false;

function clamp(value, min, max) {
  return Math.max(min, Math.min(max, value));
}

function easeInOutSine(t) {
  return -(Math.cos(Math.PI * t) - 1) / 2;
}

function wait(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

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

function getViewportSize() {
  return getViewportMetrics();
}

function updateViewportCssVars() {
  const { width, height } = getViewportMetrics();
  document.documentElement.style.setProperty("--app-vw", `${width}px`);
  document.documentElement.style.setProperty("--app-vh", `${height}px`);
}

function updateTileSize() {
  document.documentElement.style.setProperty("--tile-size", `${TILE_SIZE}px`);
}

function applyTransform(tx, ty, scale) {
  world.style.transform = `translate(${tx}px, ${ty}px) scale(${scale})`;
  currentScale = scale;
}

function getTransformForPoint(x, y, scale = FOCUS_SCALE) {
  const { width, height } = getViewportSize();

  let tx = width / 2 - x * scale;
  let ty = height / 2 - y * scale;

  const minX = width - WORLD_WIDTH * scale;
  const minY = height - WORLD_HEIGHT * scale;

  tx = clamp(tx, minX, 0);
  ty = clamp(ty, minY, 0);

  return { tx, ty, scale };
}

function animateTravel(fromStation, toStation, duration = 1700) {
  return new Promise((resolve) => {
    const startTime = performance.now();

    const startTransform = getTransformForPoint(fromStation.x, fromStation.y, FOCUS_SCALE);
    const endTransform = getTransformForPoint(toStation.x, toStation.y, FOCUS_SCALE);

    const fixedTy = startTransform.ty;

    function frame(now) {
      const elapsed = now - startTime;
      const t = clamp(elapsed / duration, 0, 1);
      const eased = easeInOutSine(t);

      const tx = startTransform.tx + (endTransform.tx - startTransform.tx) * eased;
      applyTransform(tx, fixedTy, FOCUS_SCALE);

      if (t < 1) {
        requestAnimationFrame(frame);
      } else {
        applyTransform(endTransform.tx, fixedTy, FOCUS_SCALE);
        resolve();
      }
    }

    requestAnimationFrame(frame);
  });
}

function getPlayerForChapter(index) {
  return youtubePlayers.get(index) || null;
}

function getPlayButtonForChapter(index) {
  return chapterPlayButtons.get(index) || null;
}

function getVideoFrameForChapter(index) {
  const chapter = chapters.find((item) => Number(item.dataset.index) === index);
  return chapter ? chapter.querySelector(".chapter-video-frame") : null;
}

function isMobileLayout() {
  return getViewportMetrics().width <= MOBILE_BREAKPOINT;
}

function isYoutubeAvailable() {
  return !!(window.YT && window.YT.Player && window.YT.PlayerState);
}

function isPlayerReady(index) {
  return !!playerReady.get(index);
}

function getPlayerState(index) {
  return playerStates.get(index) ?? -1;
}

function getPlayerDuration(index) {
  return playerDurations.get(index) ?? 0;
}

function setPlayButtonMode(button, mode) {
  if (!button) return;
  const icon = button.querySelector("span");
  if (!icon) return;
  icon.className = mode === "pause" ? "pause-icon" : "play-icon";
}

function clearControlsHideTimeout() {
  if (controlsHideTimeout) {
    clearTimeout(controlsHideTimeout);
    controlsHideTimeout = null;
  }
}

function clearAutoAdvanceTimeout() {
  if (autoAdvanceTimeout) {
    clearTimeout(autoAdvanceTimeout);
    autoAdvanceTimeout = null;
  }
}

function hidePlayButton(index = currentIndex) {
  const button = getPlayButtonForChapter(index);
  if (!button) return;
  button.classList.add("is-hidden");
}

function isPlayerPausedOrEnded(index) {
  if (!isYoutubeAvailable()) return true;
  const state = getPlayerState(index);
  return state !== YT.PlayerState.PLAYING;
}

function setYoutubeInteractivity(index, enabled) {
  const frame = getVideoFrameForChapter(index);
  if (!frame) return;
  frame.classList.toggle("is-youtube-interactive", enabled);
}

function disableYoutubeInteractivityAll(exceptIndex = null) {
  for (let i = 1; i <= 5; i += 1) {
    if (i === exceptIndex) continue;
    setYoutubeInteractivity(i, false);
  }
}

function showPlayButtonTemporarily(index = currentIndex) {
  if (isAnimating) return;
  if (index < 1 || index > 5) return;
  if (completedChapters.has(index)) return;
  if (index !== currentIndex) return;
  if (getVideoFrameForChapter(index)?.classList.contains("is-youtube-interactive")) return;

  const button = getPlayButtonForChapter(index);
  const player = getPlayerForChapter(index);

  if (!button || !player || !isPlayerReady(index)) return;

  setPlayButtonMode(button, isPlayerPausedOrEnded(index) ? "play" : "pause");
  button.classList.remove("is-hidden");

  clearControlsHideTimeout();
  controlsHideTimeout = setTimeout(() => {
    if (currentIndex !== index) return;
    if (completedChapters.has(index)) return;
    hidePlayButton(index);
  }, CONTROLS_VISIBLE_MS);
}

function stopProgressTracking(index) {
  const intervalId = playerProgressIntervals.get(index);
  if (intervalId) {
    clearInterval(intervalId);
    playerProgressIntervals.delete(index);
  }
}

function startProgressTracking(index) {
  stopProgressTracking(index);

  const intervalId = setInterval(() => {
    if (activeVideoChapter === index) {
      updateVideoProgress();
    }
  }, 100);

  playerProgressIntervals.set(index, intervalId);
}

function pauseAllVideos(exceptChapter = null) {
  youtubePlayers.forEach((player, chapterNumber) => {
    if (chapterNumber === exceptChapter) return;
    try {
      player.pauseVideo();
    } catch (error) {}
  });
}

function updateSoundButtonUi() {
  if (!soundToggle) return;
  soundToggle.classList.toggle("is-muted", isMuted);
  soundToggle.setAttribute("aria-label", isMuted ? "Ton aktivieren" : "Ton stummschalten");
}

function updateFullscreenButtonUi() {
  if (!fullscreenToggle) return;

  const isFs = isMobileLayout()
    ? mobileVideoFullscreenChapter != null
    : !!document.fullscreenElement;

  fullscreenToggle.classList.toggle("is-fullscreen", isFs);
  fullscreenToggle.setAttribute(
    "aria-label",
    isFs ? "Vollbild verlassen" : "Vollbild aktivieren"
  );
}

function toggleMute() {
  isMuted = !isMuted;

  youtubePlayers.forEach((player) => {
    try {
      if (isMuted) {
        player.mute();
      } else {
        player.unMute();
      }
    } catch (error) {}
  });

  updateSoundButtonUi();
}

function enterMobileVideoFullscreen(index) {
  if (!isMobileLayout()) return;
  if (index < 1 || index > 5) return;

  const frame = getVideoFrameForChapter(index);
  if (!frame) return;

  if (mobileVideoFullscreenChapter != null && mobileVideoFullscreenChapter !== index) {
    exitMobileVideoFullscreen();
  }

  document.body.classList.add("mobile-video-fullscreen");
  frame.classList.add("is-video-fullscreen");
  mobileVideoFullscreenChapter = index;
  updateViewportCssVars();
  updateFullscreenButtonUi();
}

function exitMobileVideoFullscreen() {
  if (mobileVideoFullscreenChapter == null) return;

  const frame = getVideoFrameForChapter(mobileVideoFullscreenChapter);
  if (frame) {
    frame.classList.remove("is-video-fullscreen");
  }

  document.body.classList.remove("mobile-video-fullscreen");
  mobileVideoFullscreenChapter = null;
  updateFullscreenButtonUi();
}

async function toggleVideoFullscreen() {
  if (currentIndex < 1 || currentIndex > 5) return;

  if (!isMobileLayout()) {
    try {
      if (!document.fullscreenElement) {
        await document.documentElement.requestFullscreen();
      } else {
        await document.exitFullscreen();
      }
    } catch (error) {
      console.error("Fullscreen konnte nicht umgeschaltet werden:", error);
    }
    return;
  }

  if (mobileVideoFullscreenChapter === currentIndex) {
    exitMobileVideoFullscreen();
  } else {
    enterMobileVideoFullscreen(currentIndex);
  }
}

function updateVideoProgress() {
  let activeProgressChapter = null;
  let activeProgress = 0;

  if (activeVideoChapter != null) {
    const player = getPlayerForChapter(activeVideoChapter);
    const duration = getPlayerDuration(activeVideoChapter);

    if (
      player &&
      duration &&
      Number.isFinite(duration) &&
      typeof player.getCurrentTime === "function"
    ) {
      let currentTime = 0;

      try {
        currentTime = player.getCurrentTime();
      } catch (error) {
        currentTime = 0;
      }

      activeProgressChapter = activeVideoChapter;
      activeProgress = clamp(currentTime / duration, 0, 1);
    }
  }

  videoProgressFills.forEach((fill, index) => {
    const chapterNumber = index + 1;
    let progress = 0;

    if (completedChapters.has(chapterNumber)) {
      progress = 1;
    } else if (chapterNumber === activeProgressChapter) {
      progress = activeProgress;
    }

    fill.style.width = `${progress * 100}%`;
  });
}

function updateNavLocks() {
  navItems.forEach((item) => {
    item.disabled = false;
  });
}

function updateChapterActionUi() {
  chapters.forEach((chapter) => {
    const index = Number(chapter.dataset.index);
    const dot = chapter.querySelector(".chapter-dot");
    const playButton = chapter.querySelector(".chapter-play-button");
    const frame = chapter.querySelector(".chapter-video-frame");
    const youtubeInteractive = frame?.classList.contains("is-youtube-interactive");

    if (dot && index !== 0 && index !== 6) {
      dot.classList.add("is-hidden");
    }

    if (playButton) {
      playButton.classList.add("is-hidden");
    }

    if (index === 0 || index === 6) return;
    if (index !== currentIndex) return;
    if (isAnimating) return;

    if (playButton && isPlayerReady(index) && !youtubeInteractive) {
      setPlayButtonMode(playButton, isPlayerPausedOrEnded(index) ? "play" : "pause");
    }
  });
}

function updateDots() {
  chapterDots.forEach((dot, index) => {
    if (!dot) return;

    const isStartOrEnd = index === 0 || index === 6;
    const isActive = index === currentIndex;
    const canUse = isStartOrEnd && isActive && !isAnimating;

    dot.disabled = !canUse;
  });

  chapterPlayButtons.forEach((button, index) => {
    const frame = getVideoFrameForChapter(index);
    const youtubeInteractive = frame?.classList.contains("is-youtube-interactive");
    button.disabled = !(currentIndex === index && !isAnimating && !youtubeInteractive);
  });

  updateChapterActionUi();
}

function updateActiveChapter(index) {
  currentIndex = index;

  chapters.forEach((chapter) => {
    const chapterIndex = Number(chapter.dataset.index);
    chapter.classList.toggle("active", chapterIndex === index);
  });

  navItems.forEach((item) => {
    const target = Number(item.dataset.target);
    item.classList.toggle("active", target === index);
  });

  if (titleDismissed) {
    titleChapter.classList.add("is-dismissed");
  } else {
    titleChapter.classList.remove("is-dismissed");
  }

  if (mobileVideoFullscreenChapter != null && mobileVideoFullscreenChapter !== index) {
    exitMobileVideoFullscreen();
  }

  clearControlsHideTimeout();
  updateDots();
  updateNavLocks();
  updateVideoProgress();
  updateFullscreenButtonUi();
}

function dismissTitleIfNeeded() {
  if (titleDismissed || currentIndex === 0) return;
  titleDismissed = true;
  titleChapter.classList.add("is-dismissed");
}

function waitForPlayerReady(index, timeout = PLAYER_READY_TIMEOUT_MS) {
  return new Promise((resolve) => {
    if (isPlayerReady(index)) {
      resolve(true);
      return;
    }

    const start = performance.now();

    const interval = setInterval(() => {
      if (isPlayerReady(index)) {
        clearInterval(interval);
        resolve(true);
        return;
      }

      if (performance.now() - start > timeout) {
        clearInterval(interval);
        resolve(false);
      }
    }, 50);
  });
}

async function playChapterVideo(index) {
  const player = getPlayerForChapter(index);
  const playButton = getPlayButtonForChapter(index);

  if (!player || completedChapters.has(index)) return;

  const ready = await waitForPlayerReady(index);
  if (!ready) return;

  clearAutoAdvanceTimeout();
  setYoutubeInteractivity(index, false);
  disableYoutubeInteractivityAll(index);
  pauseAllVideos(index);
  activeVideoChapter = index;

  try {
    if (isMuted) {
      player.mute();
    } else {
      player.unMute();
    }

    player.playVideo();
    setPlayButtonMode(playButton, "pause");
    startProgressTracking(index);
    updateVideoProgress();
    showPlayButtonTemporarily(index);
    updateDots();
  } catch (error) {
    console.error(`Kapitel ${index} konnte nicht abgespielt werden:`, error);
    setPlayButtonMode(playButton, "play");
    showPlayButtonTemporarily(index);
  }
}

function pauseChapterVideo(index) {
  const player = getPlayerForChapter(index);
  if (!player) return;

  try {
    player.pauseVideo();
  } catch (error) {}
}

async function autoStartVideoForCurrentChapter() {
  if (currentIndex < 1 || currentIndex > 5) return;
  if (completedChapters.has(currentIndex)) return;

  const player = getPlayerForChapter(currentIndex);
  if (!player) return;

  pauseAllVideos(currentIndex);
  await wait(120);
  await playChapterVideo(currentIndex);
}

async function moveFromTitleToChapterOne() {
  if (isAnimating || currentIndex !== 0) return;

  clearAutoAdvanceTimeout();
  disableYoutubeInteractivityAll();
  isAnimating = true;
  titleDismissed = true;
  titleChapter.classList.add("is-dismissed");

  await animateTravel(stations[0], stations[1], 1700);

  updateActiveChapter(1);
  dismissTitleIfNeeded();

  isAnimating = false;
  updateDots();

  await autoStartVideoForCurrentChapter();
}

async function moveToIndex(targetIndex) {
  if (targetIndex === currentIndex) return;
  if (targetIndex < 0 || targetIndex >= stations.length) return;

  clearAutoAdvanceTimeout();
  clearControlsHideTimeout();
  disableYoutubeInteractivityAll();
  pauseAllVideos();
  exitMobileVideoFullscreen();

  isAnimating = true;
  updateDots();

  if (targetIndex > currentIndex) {
    await animateTravel(stations[currentIndex], stations[targetIndex], 1700);
    updateActiveChapter(targetIndex);
    dismissTitleIfNeeded();
    isAnimating = false;
    updateDots();
    await autoStartVideoForCurrentChapter();
    return;
  }

  if (currentIndex === 1 && targetIndex === 0) {
    await animateTravel(stations[currentIndex], stations[targetIndex], 1700);
    titleDismissed = false;
    titleChapter.classList.remove("is-dismissed");
    updateActiveChapter(0);
    isAnimating = false;
    updateDots();
    return;
  }

  await animateTravel(stations[currentIndex], stations[targetIndex], 1700);
  updateActiveChapter(targetIndex);
  isAnimating = false;
  updateDots();
  await autoStartVideoForCurrentChapter();
}

async function finishChapterVideo(index) {
  completedChapters.add(index);
  unlockedIndex = Math.max(unlockedIndex, Math.min(index + 1, 6));
  activeVideoChapter = index;
  clearControlsHideTimeout();
  hidePlayButton(index);
  stopProgressTracking(index);
  setYoutubeInteractivity(index, false);
  updateVideoProgress();
  updateChapterActionUi();
  updateDots();
  exitMobileVideoFullscreen();

  const nextIndex = index + 1;
  if (nextIndex <= 6) {
    await moveToIndex(nextIndex);
  }
}

async function handleMainDotClick(index) {
  if (isAnimating) return;
  if (index !== currentIndex) return;

  if (index === 0) {
    await moveFromTitleToChapterOne();
    return;
  }

  if (index === 6) {
    window.location.href = "index.html";
  }
}

async function goToChapter(targetIndex) {
  if (isAnimating) return;
  if (targetIndex < 1 || targetIndex > 6) return;

  clearAutoAdvanceTimeout();
  clearControlsHideTimeout();
  disableYoutubeInteractivityAll();
  pauseAllVideos();
  exitMobileVideoFullscreen();

  if (currentIndex === 0) {
    titleDismissed = true;
    titleChapter.classList.add("is-dismissed");
  }

  await moveToIndex(targetIndex);
}

async function toggleVideoPlayback(index) {
  const player = getPlayerForChapter(index);
  const button = getPlayButtonForChapter(index);

  if (!player || completedChapters.has(index)) return;

  clearAutoAdvanceTimeout();

  if (isPlayerPausedOrEnded(index)) {
    setYoutubeInteractivity(index, false);
    await playChapterVideo(index);
    setPlayButtonMode(button, "pause");
  } else {
    pauseChapterVideo(index);
  }

  updateChapterActionUi();
  updateDots();
}

function recenterCurrentChapter() {
  const pos = stations[currentIndex];
  const transform = getTransformForPoint(pos.x, pos.y, FOCUS_SCALE);
  applyTransform(transform.tx, transform.ty, FOCUS_SCALE);
  updateDots();
  updateVideoProgress();
}

function handleViewportLayoutChange(force = false) {
  updateViewportCssVars();

  const { width, height } = getViewportMetrics();
  const deltaW = Math.abs(width - lastViewportWidth);
  const deltaH = Math.abs(height - lastViewportHeight);

  const significantChange =
    force ||
    lastViewportWidth === 0 ||
    lastViewportHeight === 0 ||
    deltaW >= VIEWPORT_REPOSITION_THRESHOLD ||
    deltaH >= VIEWPORT_REPOSITION_THRESHOLD;

  lastViewportWidth = width;
  lastViewportHeight = height;

  if (!isMobileLayout() && mobileVideoFullscreenChapter != null) {
    exitMobileVideoFullscreen();
  }

  updateFullscreenButtonUi();

  if (!significantChange) return;
  if (isAnimating) return;

  recenterCurrentChapter();
}

function scheduleViewportLayoutChange(force = false) {
  if (layoutUpdateRaf != null) {
    cancelAnimationFrame(layoutUpdateRaf);
  }

  layoutUpdateRaf = requestAnimationFrame(() => {
    layoutUpdateRaf = null;
    handleViewportLayoutChange(force);
  });
}

function handleInteractionReveal() {
  if (currentIndex < 1 || currentIndex > 5) return;
  if (completedChapters.has(currentIndex)) return;
  if (getVideoFrameForChapter(currentIndex)?.classList.contains("is-youtube-interactive")) return;
  showPlayButtonTemporarily(currentIndex);
}

function bindEvents() {
  chapterDots.forEach((dot, index) => {
    if (!dot) return;
    dot.addEventListener("click", async () => {
      await handleMainDotClick(index);
    });
  });

  chapterPlayButtons.forEach((button, index) => {
    button.addEventListener("click", async (event) => {
      event.stopPropagation();
      if (index !== currentIndex || isAnimating) return;
      await toggleVideoPlayback(index);
    });
  });

  if (soundToggle) {
    soundToggle.addEventListener("click", toggleMute);
  }

  if (fullscreenToggle) {
    fullscreenToggle.addEventListener("click", () => {
      toggleVideoFullscreen();
    });
  }

  navItems.forEach((item) => {
    item.addEventListener("click", async () => {
      const targetIndex = Number(item.dataset.target);
      await goToChapter(targetIndex);
    });
  });

  reloadProject.addEventListener("click", () => {
    window.location.href = "index.html";
  });

  document.addEventListener("mousemove", handleInteractionReveal, { passive: true });
  document.addEventListener("touchstart", handleInteractionReveal, { passive: true });

  chapters.forEach((chapter) => {
    const index = Number(chapter.dataset.index);
    const frame = chapter.querySelector(".chapter-video-frame");
    if (!frame) return;

    frame.addEventListener("click", () => {
      if (index !== currentIndex) return;
      if (frame.classList.contains("is-youtube-interactive")) return;
      handleInteractionReveal();
    });

    frame.addEventListener("touchstart", () => {
      if (index !== currentIndex) return;
      if (frame.classList.contains("is-youtube-interactive")) return;
      handleInteractionReveal();
    }, { passive: true });
  });

  window.addEventListener("keydown", async (event) => {
    if (event.key === "Escape") {
      if (mobileVideoFullscreenChapter != null) {
        exitMobileVideoFullscreen();
        return;
      }

      if (document.fullscreenElement) {
        try {
          await document.exitFullscreen();
        } catch (error) {
          console.error("Fullscreen konnte nicht beendet werden:", error);
        }
        return;
      }
    }

    if (currentIndex < 1 || currentIndex > 5) return;

    if (event.code === "Space") {
      event.preventDefault();
      if (!getVideoFrameForChapter(currentIndex)?.classList.contains("is-youtube-interactive")) {
        showPlayButtonTemporarily(currentIndex);
      }
      await toggleVideoPlayback(currentIndex);
    }
  });

  document.addEventListener("fullscreenchange", () => {
    updateFullscreenButtonUi();
  });

  window.addEventListener("orientationchange", () => {
    updateViewportCssVars();

    if (resizeSettleTimer) clearTimeout(resizeSettleTimer);
    resizeSettleTimer = setTimeout(() => {
      scheduleViewportLayoutChange(true);
    }, 350);
  });

  window.addEventListener("resize", () => {
    updateViewportCssVars();
    scheduleViewportLayoutChange(false);

    if (resizeSettleTimer) clearTimeout(resizeSettleTimer);
    resizeSettleTimer = setTimeout(() => {
      scheduleViewportLayoutChange(true);
    }, 180);
  });

  if (window.visualViewport) {
    window.visualViewport.addEventListener("resize", () => {
      updateViewportCssVars();
      scheduleViewportLayoutChange(false);

      if (resizeSettleTimer) clearTimeout(resizeSettleTimer);
      resizeSettleTimer = setTimeout(() => {
        scheduleViewportLayoutChange(true);
      }, 180);
    });

    window.visualViewport.addEventListener("scroll", () => {
      updateViewportCssVars();
      scheduleViewportLayoutChange(false);
    });
  }
}

function createPlayers() {
  if (playersCreated) return;
  if (!isYoutubeAvailable()) return;

  playersCreated = true;

  playerHostMap.forEach((host, index) => {
    const videoId = YOUTUBE_VIDEO_IDS[index];

    if (!videoId) {
      console.warn(`Für Kapitel ${index} fehlt eine YouTube-Video-ID.`);
      return;
    }

    const player = new YT.Player(host.id, {
      videoId,
      host: "https://www.youtube.com",
      playerVars: {
        autoplay: 0,
        controls: 0,
        disablekb: 1,
        fs: 0,
        modestbranding: 1,
        rel: 0,
        iv_load_policy: 3,
        playsinline: 1,
        origin: window.location.origin
      },
      events: {
        onReady: (event) => {
          playerReady.set(index, true);

          try {
            if (isMuted) {
              event.target.mute();
            } else {
              event.target.unMute();
            }

            const duration = event.target.getDuration();
            if (Number.isFinite(duration) && duration > 0) {
              playerDurations.set(index, duration);
            }

            event.target.cueVideoById(videoId);
            setYoutubeInteractivity(index, false);
          } catch (error) {}
        },

        onStateChange: async (event) => {
          const state = event.data;
          playerStates.set(index, state);

          try {
            const duration = event.target.getDuration();
            if (Number.isFinite(duration) && duration > 0) {
              playerDurations.set(index, duration);
            }
          } catch (error) {}

          if (state === YT.PlayerState.PLAYING) {
            clearAutoAdvanceTimeout();
            activeVideoChapter = index;
            setYoutubeInteractivity(index, false);
            disableYoutubeInteractivityAll(index);
            const button = getPlayButtonForChapter(index);
            setPlayButtonMode(button, "pause");
            startProgressTracking(index);
            updateVideoProgress();
            updateChapterActionUi();
            updateDots();
          }

          if (state === YT.PlayerState.PAUSED) {
            if (!completedChapters.has(index)) {
              setYoutubeInteractivity(index, true);
            }
            stopProgressTracking(index);
            updateVideoProgress();
            updateChapterActionUi();
            updateDots();
          }

          if (state === YT.PlayerState.ENDED) {
            stopProgressTracking(index);
            await finishChapterVideo(index);
          }

          if (state === YT.PlayerState.CUED) {
            if (!completedChapters.has(index)) {
              setYoutubeInteractivity(index, true);
              updateChapterActionUi();
              updateDots();
            }
          }
        }
      }
    });

    youtubePlayers.set(index, player);
    playerStates.set(index, -1);
  });
}

function setupYoutubeApiHook() {
  if (isYoutubeAvailable()) {
    createPlayers();
    return;
  }

  const previousHandler = window.onYouTubeIframeAPIReady;

  window.onYouTubeIframeAPIReady = function () {
    if (typeof previousHandler === "function") {
      try {
        previousHandler();
      } catch (error) {}
    }
    createPlayers();
  };

  let attempts = 0;
  const maxAttempts = 120;

  const poll = setInterval(() => {
    attempts += 1;

    if (isYoutubeAvailable()) {
      clearInterval(poll);
      createPlayers();
      return;
    }

    if (attempts >= maxAttempts) {
      clearInterval(poll);
      console.warn("YouTube API wurde nicht geladen.");
    }
  }, 100);
}

async function initialize() {
  completedChapters = new Set();
  unlockedIndex = 1;

  updateTileSize();
  updateViewportCssVars();
  updateSoundButtonUi();
  updateFullscreenButtonUi();

  const startTransform = getTransformForPoint(stations[0].x, stations[0].y, FOCUS_SCALE);
  applyTransform(startTransform.tx, startTransform.ty, FOCUS_SCALE);

  const initialViewport = getViewportMetrics();
  lastViewportWidth = initialViewport.width;
  lastViewportHeight = initialViewport.height;

  if (document.fonts && document.fonts.ready) {
    await document.fonts.ready;
  }

  updateActiveChapter(0);
  bindEvents();
  setupYoutubeApiHook();

  await nextFrame();
  revealApp();
}

initialize();