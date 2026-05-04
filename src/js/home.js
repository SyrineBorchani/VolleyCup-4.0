const eventDate = new Date("2026-12-05T09:00:00+01:00");

function formatDeadline(date) {
  const month = date.toLocaleString("en-US", { month: "long" });
  const day = date.getDate();

  function getOrdinal(value) {
    if (value > 3 && value < 21) {
      return "th";
    }

    switch (value % 10) {
      case 1:
        return "st";
      case 2:
        return "nd";
      case 3:
        return "rd";
      default:
        return "th";
    }
  }

  return {
    plain: month + " " + day + getOrdinal(day),
    html: month + " " + day + String(getOrdinal(day)).sup()
  };
}

function renderPromoBadge() {
  const heroAlerts = document.getElementById("heroAlerts");

  if (!heroAlerts) {
    return;
  }

  const registrationDeadline = new Date(eventDate);
  registrationDeadline.setDate(registrationDeadline.getDate() - 4);

  const badge = document.createElement("p");
  badge.className = "promo-badge";

  const dot = document.createElement("span");
  dot.className = "promo-badge__dot";
  dot.setAttribute("aria-hidden", "true");

  const deadline = formatDeadline(registrationDeadline);
  const message = document.createElement("span");
  const alertText = "Limited spots left".bold().fontcolor("#fff7e8").big();
  const deadlineText = (" Register before " + deadline.html + ".").italics();

  message.innerHTML = alertText + deadlineText;

  badge.appendChild(dot);
  badge.appendChild(message);
  heroAlerts.replaceChildren(badge);
}

function setupGalleryImageDemo() {
  const mainGalleryImage = document.getElementById("mainGalleryImage");

  if (!mainGalleryImage) {
    return;
  }

  const galleryImages = Array.from(document.querySelectorAll(".gallery-grid img"))
    .map(function(image) {
      return image.getAttribute("src") || "";
    })
    .filter(function(source, index, sources) {
      return source !== "" && sources.indexOf(source) === index;
    });

  if (galleryImages.length < 2) {
    return;
  }

  mainGalleryImage.addEventListener("click", function() {
    const currentSource = mainGalleryImage.getAttribute("src") || "";
    const currentIndex = galleryImages.indexOf(currentSource);
    const nextIndex = currentIndex === -1 ? 0 : (currentIndex + 1) % galleryImages.length;
    const nextSource = galleryImages[nextIndex];

    mainGalleryImage.setAttribute("src", nextSource);
  });
}

function startCountdown() {
  const countdown = document.getElementById("countdown");

  if (!countdown) {
    return;
  }

  function updateCountdown() {
    const now = new Date();
    const distance = eventDate - now;

    if (distance <= 0) {
      countdown.textContent = "VolleyCup 4.0 is happening now.";
      return;
    }

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
    const minutes = Math.floor((distance / (1000 * 60)) % 60);

    countdown.textContent = days + " days, " + hours + " hours, and " + minutes + " minutes until kickoff.";
  }

  updateCountdown();
  setInterval(updateCountdown, 1000);
}

function animateStats() {
  const numbers = document.querySelectorAll(".stat .number[data-target]");

  if (!numbers.length) {
    return;
  }

  function runCounter(element) {
    const target = Number(element.dataset.target || 0);
    const prefix = element.dataset.prefix || "";
    const suffix = element.dataset.suffix || "";
    const duration = 1400;
    const startTime = performance.now();

    function step(currentTime) {
      const progress = Math.min((currentTime - startTime) / duration, 1);
      const currentValue = Math.floor(target * progress);

      element.textContent = prefix + currentValue + suffix;

      if (progress < 1) {
        requestAnimationFrame(step);
      } else {
        element.textContent = prefix + target + suffix;
      }
    }

    requestAnimationFrame(step);
  }

  const observer = new IntersectionObserver(function(entries, currentObserver) {
    entries.forEach(function(entry) {
      if (!entry.isIntersecting) {
        return;
      }

      runCounter(entry.target);
      currentObserver.unobserve(entry.target);
    });
  }, { threshold: 0.45 });

  numbers.forEach(function(number) {
    observer.observe(number);
  });
}

document.addEventListener("DOMContentLoaded", function() {
  renderPromoBadge();
  setupGalleryImageDemo();
  startCountdown();
  animateStats();
});
