(() => {
  // Dark mode
  const defaultTheme = window.localStorage.getItem("theme") ?? "auto";

  // Edit `data-bs-theme` attribute to html tag
  const setColorTheme = (theme) => {
    document.getElementsByTagName("html")[0].dataset.bsTheme = theme;
  };

  // Get browser current theme
  const getBrowserDefaultTheme = () => {
    if (
      window.matchMedia &&
      window.matchMedia("(prefers-color-scheme: dark)").matches
    ) {
      return "dark";
    }

    return "light";
  };

  // Change theme icon on navbar
  const setToggleIcon = (theme) => {
    let icon;
    switch (theme) {
      case "dark":
        icon = "moon-stars";
        break;
      case "light":
        icon = "sun";
        break;
      default:
        icon = "circle-half";
        break;
    }
    return (document.querySelector(
      "[data-current-icon] i"
    ).classList = `bi bi-${icon}`);
  };

  // button theme from dropdown
  const themeButtons = document.querySelectorAll("[data-theme-toggler]");

  if (defaultTheme === "auto") {
    if (
      window.matchMedia &&
      window.matchMedia("(prefers-color-scheme: dark)").matches
    ) {
      setColorTheme("dark");
    } else {
      setColorTheme("light");
    }
  }

  // Change active theme button on dropdown
  const changeActiveTheme = (theme) => {
    const icon = document.querySelector(`[data-theme-value="${theme}"]`);
    const button = icon.closest("button");
    const activeTheme = document.querySelector("[data-theme-toggler].active");

    setColorTheme(theme === "auto" ? getBrowserDefaultTheme() : theme);

    if (activeTheme === button) return;
    activeTheme.classList.remove("active");
    button.classList.add("active");

    return;
  };

  // Handle click on theme buttons in dropdown
  const handleClick = (event) => {
    event.preventDefault();
    const icon =
      event.target.tagName === "I"
        ? event.target
        : event.target.querySelector("i");

    const button =
      event.target.tagName === "BUTTON"
        ? event.target
        : event.target.closest("button");

    const theme = icon.dataset.themeValue;

    changeActiveTheme(theme);
    setToggleIcon(theme);
    window.localStorage.setItem("theme", theme);
  };

  if (themeButtons) {
    themeButtons.forEach((button) => {
      button.addEventListener("click", handleClick);
    });
  }

  changeActiveTheme(defaultTheme);
  setToggleIcon(defaultTheme);
})();
