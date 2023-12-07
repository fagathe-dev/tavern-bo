(() => {
  const links = document.querySelectorAll("[data-href]");

  if (links) {
    links.forEach((link) => {
      link.addEventListener("click", (e) => {
        const el =
          e.target.tagName === "I" ? e.target.closest("[data-href]") : e.target;
        return (window.location = el.dataset.href);
      });
    });
  }

  const url = new URL(window.location);

  const filters = document.querySelectorAll("[data-filter]");
  if (filters) {
    filters.forEach((filter) => {
      const fn = filter.dataset.filterName;
      const fv = filter.dataset.filterValue;

      if (url.searchParams.has(fn)) {
        url.searchParams.get(fn) === fv &&
          document
            .querySelector(
              `[data-filter-name="${fn}"][data-filter-value="${fv}"]`
            )
            .classList.add("active");
      }

      filter.addEventListener("click", (e) => {
        const filterName = e.target.dataset.filterName;
        const filterValue = e.target.dataset.filterValue;
        console.info(e.target.dataset);
        url.searchParams.set(filterName, filterValue);

        window.location = url;
      });
    });
  }
})();
