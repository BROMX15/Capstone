document.addEventListener("DOMContentLoaded", () => {
  const itemSearch = document.getElementById("itemSearch");
  const suggestions = document.getElementById("suggestions");
  const itemNameInput = document.getElementById("itemName");
  const unitPriceInput = document.getElementById("unitPrice");
  const priceInfo = document.getElementById("priceInfo");

  // ================= MOCK DATA WITH MULTIPLE DISTRIBUTORS =================
  const mockParts = [
    {
      mpn: "BAV99",
      manufacturer: "Vishay",
      shortDescription: "Dual Switching Diode",
      distributors: [
        { name: "Digi-Key", price: 0.05, quantity: 100, currency: "USD" },
        { name: "Mouser", price: 0.048, quantity: 200, currency: "USD" },
        { name: "Arrow", price: 0.046, quantity: 500, currency: "USD" },
      ],
    },
    {
      mpn: "LM358",
      manufacturer: "Texas Instruments",
      shortDescription: "Dual Operational Amplifier",
      distributors: [
        { name: "Digi-Key", price: 0.35, quantity: 50, currency: "USD" },
        { name: "Mouser", price: 0.33, quantity: 100, currency: "USD" },
      ],
    },
    {
      mpn: "1N4007",
      manufacturer: "ON Semiconductor",
      shortDescription: "Standard Rectifier Diode",
      distributors: [
        { name: "Digi-Key", price: 0.02, quantity: 100, currency: "USD" },
        { name: "Mouser", price: 0.018, quantity: 500, currency: "USD" },
      ],
    },
    {
      mpn: "STM32F103",
      manufacturer: "STMicroelectronics",
      shortDescription: "32-bit ARM Cortex-M3 MCU",
      distributors: [
        { name: "Digi-Key", price: 2.5, quantity: 10, currency: "USD" },
        { name: "Mouser", price: 2.45, quantity: 50, currency: "USD" },
      ],
    },
  ];

  // ================= SEARCH FUNCTION (MOCK) =================
  async function searchParts(query) {
    if (query.length < 3) return [];
    const q = query.toLowerCase();
    return mockParts.filter(
      (p) =>
        p.mpn.toLowerCase().includes(q) ||
        p.shortDescription.toLowerCase().includes(q)
    );
  }

  // ================= POPULATE DISTRIBUTOR SELECT =================
  function populateDistributors(distributors) {
    // Remove old distributor select if exists
    let oldSelect = document.getElementById("distributorSelect");
    if (oldSelect) oldSelect.remove();

    if (!distributors.length) return;

    const div = document.createElement("div");
    div.className = "form-group";
    div.id = "distributorWrapper";
    div.innerHTML = `
      <label for="distributorSelect">Select Distributor</label>
      <select id="distributorSelect"></select>
    `;

    const select = div.querySelector("select");
    distributors.forEach((d, idx) => {
      const option = document.createElement("option");
      option.value = idx;
      option.textContent = `${d.name} — $${d.price.toFixed(4)} @ ${
        d.quantity
      } units`;
      select.appendChild(option);
    });

    // Update price when distributor changes
    select.addEventListener("change", () => {
      const selected = distributors[select.value];
      unitPriceInput.value = selected.price.toFixed(4);
      priceInfo.textContent = `Price @ ${selected.quantity} units (${selected.currency}) — Mock Data`;
    });

    // Insert after item search wrapper
    const wrapper = document.getElementById("itemSearchWrapper");
    wrapper.insertAdjacentElement("afterend", div);

    // Set initial price to first distributor
    const first = distributors[0];
    unitPriceInput.value = first.price.toFixed(4);
    priceInfo.textContent = `Price @ ${first.quantity} units (${first.currency}) — Mock Data`;
  }

  // ================= LIVE SEARCH =================
  itemSearch.addEventListener("input", async () => {
    const query = itemSearch.value.trim();
    suggestions.innerHTML = "";
    suggestions.style.display = "none";
    if (query.length < 3) return;

    const results = await searchParts(query);

    if (!results.length) {
      suggestions.innerHTML = `<div class="suggestion-item">No parts found</div>`;
      suggestions.style.display = "block";
      return;
    }

    results.forEach((part) => {
      const item = document.createElement("div");
      item.className = "suggestion-item";
      item.innerHTML = `
        <strong>${part.mpn}</strong> — ${part.manufacturer}<br>
        <small>${part.shortDescription}</small>
      `;

      item.addEventListener("click", () => {
        itemSearch.value = `${part.mpn} (${part.manufacturer})`;
        itemNameInput.value = part.mpn;

        // Populate distributors and price
        populateDistributors(part.distributors);

        suggestions.style.display = "none";
      });

      suggestions.appendChild(item);
    });

    suggestions.style.display = "block";
  });

  // ================= CLICK OUTSIDE =================
  document.addEventListener("click", (e) => {
    if (!itemSearch.parentElement.contains(e.target)) {
      suggestions.style.display = "none";
    }
  });

  // ================= FORM SUBMISSION =================
  document
    .getElementById("createRequestForm")
    .addEventListener("submit", (e) => {
      e.preventDefault();
      if (!itemNameInput.value) {
        alert("Please select a part from the suggestions");
        return;
      }

      const qty = document.getElementById("quantity").value;
      const category = document.getElementById("category").value;
      const currency = document.getElementById("currency").value;
      const urgency = document.getElementById("urgency").value;
      const reason = document.getElementById("reason").value;
      const notes = document.getElementById("notes").value;
      const reqDate = document.getElementById("requiredDate").value;
      const distributorSelect = document.getElementById("distributorSelect");
      const selectedDistributor = distributorSelect
        ? distributorSelect.options[distributorSelect.value].textContent
        : "N/A";

      alert(`Request Submitted!\n
Part: ${itemNameInput.value}
Quantity: ${qty}
Category: ${category}
Price: ${unitPriceInput.value} ${currency}
Distributor: ${selectedDistributor}
Urgency: ${urgency}
Reason: ${reason}
Notes: ${notes}
Required By: ${reqDate}`);
    });
});
