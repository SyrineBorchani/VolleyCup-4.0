function getFormFields() {
  return Array.from(document.querySelectorAll("#registrationForm input[type='text'], #registrationForm input[type='number'], #registrationForm input[type='email'], #registrationForm input[type='tel'], #registrationForm textarea"));
}

function getErrorElement(fieldId) {
  return document.querySelector("[data-error-for='" + fieldId + "']");
}

function getServerMessageElement() {
  return document.getElementById("serverMessage");
}

function clearServerMessage() {
  const message = getServerMessageElement();

  if (!message) {
    return;
  }

  message.textContent = "";
  message.style.display = "none";
}

function setServerMessage(text) {
  const message = getServerMessageElement();

  if (!message) {
    return;
  }

  message.textContent = text;
  message.style.display = "block";
}

function setFieldState(field, message) {
  const errorElement = getErrorElement(field.id);

  if (message) {
    field.style.borderColor = "#ff7b72";

    if (errorElement) {
      errorElement.textContent = message;
    }

    return;
  }

  field.style.borderColor = "";

  if (errorElement) {
    errorElement.textContent = "";
  }
}

function setServerFieldErrors(errors) {
  Object.keys(errors).forEach(function(key) {
    const field = document.getElementById(key);
    const errorElement = getErrorElement(key);

    if (field) {
      setFieldState(field, errors[key]);
    } else if (errorElement) {
      errorElement.textContent = errors[key];
    }
  });
}

function clearAllErrors() {
  getFormFields().forEach(function(field) {
    setFieldState(field, "");
  });

  ["category", "services"].forEach(function(key) {
    const errorElement = getErrorElement(key);

    if (errorElement) {
      errorElement.textContent = "";
    }
  });
}

function isFormEmpty() {
  return getFormFields().every(function(field) {
    return !field.value.trim();
  });
}

function validateField(field) {
  const value = field.value.trim();

  switch (field.id) {
    case "uniName":
    case "captain":
      if (!value) {
        return "This field is required.";
      }
      if (value.length < 3) {
        return "Please enter at least 3 characters.";
      }
      break;
    case "roster":
      if (!value) {
        return "This field is required.";
      }
      if (Number(value) < 6 || Number(value) > 15) {
        return "Roster size must be between 6 and 15 players.";
      }
      break;
    case "email":
      if (!value) {
        return "This field is required.";
      }
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
        return "Enter a valid email address.";
      }
      break;
    case "phone":
      if (!value) {
        return "This field is required.";
      }
      if (!/^[+\d\s()\-]{8,20}$/.test(value)) {
        return "Enter a valid phone number.";
      }
      break;
    case "comments":
      if (value.length > 600) {
        return "Comments must stay under 600 characters.";
      }
      break;
    default:
      if (!value) {
        return "This field is required.";
      }
  }

  return "";
}

function validateForm() {
  const fields = getFormFields();
  let firstError = null;

  fields.forEach(function(field) {
    const message = validateField(field);
    setFieldState(field, message);

    if (!firstError && message) {
      firstError = {
        field: field,
        message: message
      };
    }
  });

  return firstError;
}

function playTransitionSound(onFinish) {
  const AudioContextClass = window.AudioContext || window.webkitAudioContext;

  if (!AudioContextClass) {
    onFinish();
    return;
  }

  const audioContext = new AudioContextClass();
  const oscillator = audioContext.createOscillator();
  const gainNode = audioContext.createGain();

  oscillator.type = "square";
  oscillator.connect(gainNode);
  gainNode.connect(audioContext.destination);

  oscillator.frequency.setValueAtTime(392, audioContext.currentTime);
  oscillator.frequency.exponentialRampToValueAtTime(659.25, audioContext.currentTime + 0.18);
  oscillator.frequency.exponentialRampToValueAtTime(987.77, audioContext.currentTime + 0.36);

  gainNode.gain.setValueAtTime(0.0001, audioContext.currentTime);
  gainNode.gain.exponentialRampToValueAtTime(0.22, audioContext.currentTime + 0.04);
  gainNode.gain.exponentialRampToValueAtTime(0.0001, audioContext.currentTime + 0.55);

  oscillator.start(audioContext.currentTime);
  oscillator.stop(audioContext.currentTime + 0.55);

  oscillator.addEventListener("ended", function() {
    audioContext.close();
    onFinish();
  });
}

async function submitForm(form) {
  const submitButton = document.getElementById("submitButton");
  const formData = new FormData(form);

  clearServerMessage();
  clearAllErrors();
  submitButton.disabled = true;
  submitButton.textContent = "Submitting...";

  try {
    const response = await fetch(form.action, {
      method: "POST",
      body: formData,
      headers: {
        "X-Requested-With": "fetch"
      }
    });

    const result = await response.json();

    if (!response.ok || !result.ok) {
      if (result.errors) {
        setServerFieldErrors(result.errors);
      }

      setServerMessage(result.message || "We could not save your registration right now.");
      return;
    }

    window.location.href = "success.php?id=" + encodeURIComponent(result.registrationId);
  } catch (error) {
    setServerMessage("The registration service is unavailable right now. Make sure the PHP server is running.");
  } finally {
    submitButton.disabled = false;
    submitButton.textContent = "Submit Registration";
  }
}

function handleSubmit(event) {
  const form = event.currentTarget;

  event.preventDefault();
  clearServerMessage();

  if (isFormEmpty()) {
    window.alert("Please fill in the form.");
    validateForm();
    return;
  }

  const error = validateForm();

  if (error) {
    window.alert("Please correct the highlighted fields.");
    error.field.focus();
    return;
  }

  playTransitionSound(function() {
    submitForm(form);
  });
}

function attachLiveValidation() {
  const fields = getFormFields();

  fields.forEach(function(field) {
    field.addEventListener("input", function() {
      const message = validateField(field);
      setFieldState(field, message);
    });
  });
}

document.addEventListener("DOMContentLoaded", function() {
  const form = document.getElementById("registrationForm");

  if (!form) {
    return;
  }

  attachLiveValidation();
  form.addEventListener("submit", handleSubmit);

  const resetButton = document.getElementById("resetButton");
  if (resetButton) {
    resetButton.addEventListener("click", function() {
      form.reset();
      clearAllErrors();
      clearServerMessage();
    });
  }
});
